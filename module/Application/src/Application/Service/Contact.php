<?php
/**
 * TheStudnet (http://thestudnet.com).
 *
 * Contact
 */
namespace Application\Service;

use Dal\Service\AbstractService;
use Zend\Db\Sql\Predicate\IsNull;

/**
 * Class Contact.
 */
class Contact extends AbstractService
{
    /**
     * Request Contact.
     *
     * @invokable
     *
     * @param int $user
     *
     * @return int
     */
    public function add($user)
    {
        $identity = $this->getServiceUser()->getIdentity();
        $user_id = $identity['id'];
        if ($user == $user_id) {
            throw new \Exception("You can't add yourself");
        }

        $date = (new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s');

        $m_contact = $this->getModel()
            ->setUserId($identity['id'])
            ->setContactId($user);

        $m_contact_me = $this->getModel()
            ->setRequestDate($date)
            ->setAcceptedDate(new IsNull())
            ->setDeletedDate(new IsNull())
            ->setRequested(true)
            ->setAccepted(false)
            ->setDeleted(false);

        $m_contact_you = $this->getModel()
            ->setRequestDate($date)
            ->setAcceptedDate(new IsNull())
            ->setDeletedDate(new IsNull())
            ->setRequested(false)
            ->setAccepted(false)
            ->setDeleted(false);

        if ($this->getMapper()->select($m_contact)->count() === 0
        ) {
            $m_contact_me->setUserId($identity['id'])->setContactId($user);
            $m_contact_you->setUserId($user)->setContactId($identity['id']);
            $this->getMapper()->insert($m_contact_me);
            $ret = $this->getMapper()->insert($m_contact_you);
        } else {
            $this->getMapper()->update(
                $m_contact_me,
                array(
                'user_id' => $identity['id'],
                'contact_id' => $user,
                )
            );
            $ret = $this->getMapper()->update(
                $m_contact_you,
                array(
                'user_id' => $user,
                'contact_id' => $identity['id'],
                )
            );
        }

        $m_user = $this->getServiceUser()->getLite($user_id);
        $m_contact = $this->getServiceUser()->getLite($user);
        $name = "";
        if (!is_object($m_user->getNickname()) &&  null !== $m_user->getNickname()) {
            $name = $m_user->getNickname();
        } else {
            if (!is_object($m_user->getFirstname()) &&  null !== $m_user->getFirstname()) {
                $name = $m_user->getFirstname();
            }
            if (!is_object($m_user->getLastname()) &&  null !== $m_user->getLastname()) {
                $name .= ' '.$m_user->getLastname();
            }
        }

        /*
         $gcm_notification = new GcmNotification();
            $gcm_notification->setTitle($name)
                ->setSound("default")
                ->setColor("#00A38B")
                ->setBody('Sent you a connection request');

            $this->getServiceFcm()->send(
                $user, [
                'data' => [
                    'type' => 'connection',
                    'data' => [
                        'state' => 'request',
                        'user' => $user_id,
                    ],
                ],

                ], $gcm_notification
            );
        */
        $l = 'C'.(($user > $user_id) ? $user_id.'_'.$user : $user.'_'.$user_id);
        $this->getServicePost()->addSys(
            $l,
            'Sent you a connection request',
            ['state' => 'request','user' => $user_id,'contact' => $user],
            'request',
            ['M'.$user],
            null,
            null,
            null,
            'connection'
        );
        $this->getServiceMail()->sendTpl(
            'tpl_newrequest', $m_contact->getEmail(), [
            'firstname' =>$m_contact->getFirstname() instanceof IsNull ? $m_contact->getEmail() : $m_contact->getFirstname(),
            'contactfirstname' => $m_user->getFirstname(),
            'contactlastname' => $m_user->getLastName()
            ]
        );
        return $ret;
    }

    /**
     * Accept Contact.
     *
     * @invokable
     *
     * @param int $user
     *
     * @return bool
     */
    public function accept($user)
    {
        $identity = $this->getServiceUser()->getIdentity();
        $user_id = $identity['id'];
        $date = (new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s');

        $m_contact = $this->getModel()
            ->setAcceptedDate($date)
            ->setAccepted(false);
        $this->getMapper()->update(
            $m_contact,
            array(
            'user_id' => $user,
            'contact_id' => $user_id,
            )
        );

        $m_contact = $this->getModel()
            ->setAcceptedDate($date)
            ->setAccepted(true);
        $this->getMapper()->update(
            $m_contact,
            array(
            'user_id' => $user_id,
            'contact_id' => $user,
            )
        );

        $this->getServiceSubscription()->add('PU'.$user, $user_id);
        $this->getServiceSubscription()->add('PU'.$user_id, $user);

        $m_user = $this->getServiceUser()->getLite($user_id);
        $name = "";
        if (!is_object($m_user->getNickname()) &&  null !== $m_user->getNickname()) {
            $name = $m_user->getNickname();
        } else {
            if (!is_object($m_user->getFirstname()) &&  null !== $m_user->getFirstname()) {
                $name = $m_user->getFirstname();
            }
            if (!is_object($m_user->getLastname()) &&  null !== $m_user->getLastname()) {
                $name .= ' '.$m_user->getLastname();
            }
        }
        
        /*
                $gcm_notification = new GcmNotification();
                $gcm_notification->setTitle($name)
                    ->setSound("default")
                    ->setColor("#00A38B")
                    ->setBody('Accepted your request');
        
                $this->getServiceFcm()->send(
                    $user, ['data' => [
                    'type' => 'connection',
                    'data' => [
                        'state' => 'accept',
                        'user' => $user_id,
                        ]
                    ]
                    ], $gcm_notification
                );
        */
        $l = 'C'.(($user > $user_id) ? $user_id.'_'.$user : $user.'_'.$user_id);
        $this->getServicePost()->updateSys(
            $l,
            'Accepted your request',
            [
              'state' => 'accept',
              'user' => $user_id,
              'contact' => $user,
            ],
            'accept',
            ['M'.$user_id, 'M'.$user]
        );

        return true;
    }

    /**
     * Remove Contact
     *
     * @invokable
     *
     * @param int $user
     *
     * @return bool
     */
    public function remove($user)
    {
        $identity = $this->getServiceUser()->getIdentity();
        $user_id = $identity['id'];
        $date = (new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s');

        $m_contact = $this->getModel()
            ->setDeletedDate($date)
            ->setDeleted(false);
        $this->getMapper()->update(
            $m_contact,
            array(
            'user_id' => $user,
            'contact_id' => $user_id,
            )
        );

        $m_contact = $this->getModel()
            ->setDeletedDate($date)
            ->setDeleted(true);
        $this->getMapper()->update(
            $m_contact,
            array(
            'user_id' => $user_id,
            'contact_id' => $user,
            )
        );

        $this->getServiceSubscription()->delete('PU'.$user, $user_id);
        $this->getServiceSubscription()->delete('PU'.$user_id, $user);
        /*  $this->getServiceFcm()->send(
              $user, ['data' => [
              'type' => 'connection',
              'data' => [
                  'state' => 'remove',
                  'user' => $user_id,
                  ]
              ]
              ]
          );*/
        $this->getServiceEvent()->sendData($user_id, 'connection.remove', ['SU'.$user]);
        $l = 'C'.(($user > $user_id) ? $user_id.'_'.$user : $user.'_'.$user_id);
        $this->getServicePost()->hardDelete($l);

        return true;
    }

    /**
     * Get List Id of contact.
     *
     * @invokable
     *
     * @param int $user_id
     * @param int $search
     * @param int $exclude
     * @param int $filter
     *
     * @return array
     */
    public function getListId($user_id = null, $search = null, $exclude = null, $filter = null)
    {
        $identity = $this->getServiceUser()->getIdentity();
        if (null === $user_id) {
            $user_id = $identity['id'];
        }
        if (null !== $exclude && !is_array($exclude)) {
            $exclude = [$exclude];
        }

        $mapper = $this->getMapper();
        $res_contact = $mapper->usePaginator($filter)->getList($user_id, $exclude, $search);
        $ret = [];

        if (is_array($user_id)) {
            foreach ($user_id as $id) {
                $ret[$id] = [];
            }
            foreach ($res_contact as $m_contact) {
                $ret[$m_contact->getUserId()][] = $m_contact->getContactId();
            }
        } else {
            foreach ($res_contact as $m_contact) {
                $ret[] = $m_contact->getContactId();
            }
        }

        return (null === $filter) ?
          $ret : [
          'list' => $ret,
          'count' => $mapper->count()
          ];
    }

    /**
     * Get list contact id by users.
     *
     * @invokable
     *
     * @param int|array $user_id
     * @param int|array $contact_id
     *
     * @return \Dal\Db\ResultSet\ResultSet
     */
    public function getListRequestId($user_id = null, $contact_id = null)
    {
        if ((null === $user_id && null === $contact_id) ||  (null !== $user_id && null !== $contact_id)) {
            throw new \Exception("Error Params user and contact", 1);
        }
        $res_contact = $this->getMapper()->getListRequest($user_id, $contact_id);

        $cu_id = (null !== $user_id) ? $user_id:$contact_id;

        $request = [];
        if (is_array($cu_id)) {
            foreach ($cu_id as $cu) {
                $request[$cu] = [];
            }
            if (null === $user_id) {
                foreach ($res_contact as $m_contact) {
                    $request[$m_contact->getContactId()][] = $m_contact->getUserId();
                }
            } else {
                foreach ($res_contact as $m_contact) {
                    $request[$m_contact->getUserId()][] = $m_contact->getContactId();
                }
            }
        } else {
            if (null === $user_id) {
                foreach ($res_contact as $m_contact) {
                    $request[] = $m_contact->getUserId();
                }
            } else {
                foreach ($res_contact as $m_contact) {
                    $request[] = $m_contact->getContactId();
                }
            }
        }

        return $request;
    }
    
     /**
      * Get page counts.
      *
      * @invokable
      *
      * @param string $start_date
      * @param string $end_date
      * @param string $interval_date
      * @param int|array $page_id
      *
      * @return array
      */
    public function getRequestsCount( $start_date = null, $end_date = null, $interval_date = 'D',  $page_id  = null)
    {
        
        $interval = $this->getServiceActivity()->interval($interval_date);
        $identity = $this->getServiceUser()->getIdentity();
        
        return $this->getMapper()->getRequestsCount($identity['id'], $interval, $start_date, $end_date, $page_id);
    }
    
     /**
      * Get page counts.
      *
      * @invokable
      *
      * @param string $start_date
      * @param string $end_date
      * @param string $interval_date
      * @param int|array $page_id
      *
      * @return array
      */
    public function getAcceptedCount( $start_date = null, $end_date = null, $interval_date = 'D',  $page_id  = null)
    {
        
        $interval = $this->getServiceActivity()->interval($interval_date);
        $identity = $this->getServiceUser()->getIdentity();
        
        return $this->getMapper()->getAcceptedCount($identity['id'], $interval, $start_date, $end_date, $page_id);
    }

    /**
     * Get Service Subscription
     *
     * @return \Application\Service\Subscription
     */
    private function getServiceSubscription()
    {
        return $this->container->get('app_service_subscription');
    }

    /**
     * Get Service User.
     *
     * @return \Application\Service\User
     */
    private function getServiceUser()
    {
        return $this->container->get('app_service_user');
    }

    /**
     * Get Service Service Conversation User
     *
     * @return \Application\Service\Fcm
     */
    /*private function getServiceFcm()
    {
        return $this->container->get('fcm');
    }*/

    /**
     * Get Service Event
     *
     * @return \Application\Service\Event
     */
    private function getServiceEvent()
    {
        return $this->container->get('app_service_event');
    }

    /**
     * Get Service Post
     *
     * @return \Application\Service\Post
     */
    private function getServicePost()
    {
        return $this->container->get('app_service_post');
    }
    
    
    /**
     * Get Service Activity
     *
     * @return \Application\Service\Activity
     */
    private function getServiceActivity()
    {
        return $this->container->get('app_service_activity');
    }    

    /**
     * Get Service Mail.
     *
     * @return \Mail\Service\Mail
     */
    private function getServiceMail()
    {
        return $this->container->get('mail.service');
    }
}

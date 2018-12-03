<?php
/**
 * TheStudnet (http://thestudnet.com).
 *
 * Contact
 */
namespace Application\Service;

use Dal\Service\AbstractService;
use Zend\Db\Sql\Predicate\IsNull;
use ZendService\Google\Gcm\Notification as GcmNotification;

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

        $this->getServiceEvent()->create('connection', 'request',
              ['M'.$user],
              [
                'state' => 'request','user' => $user_id,'contact' => $user,
                'picture' => !empty($identity['avatar']) ? $identity['avatar'] : null,
                'target' => $user
              ],
              [
                'source' => $identity['firstname'].' '.$identity['lastname']
              ],   ['fcm' => Fcm::PACKAGE_TWIC_APP, 'mail' => false] );

        return $ret;
    }

    /**
     * Request Contact.
     *
     * @invokable
     *
     * @param int $user
     *
     * @return int
     */
    public function follow($user)
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

        $ret = 0;
        $res_contact = $this->getMapper()->select($m_contact);
        if ($res_contact->count() === 0) {
            $m_contact->setRequestDate($date);
            $ret = $this->getMapper()->insert($m_contact);
            $this->getServiceEvent()->create('user', 'follow',
                  ['M'.$user],
                  [
                    'state' => 'request','user' => $user_id,'contact' => $user,
                    'picture' => !empty($identity['avatar']) ? $identity['avatar'] : null,
                    'target' => $user
                  ],
                  [
                    'source' => $identity['firstname'].' '.$identity['lastname']
                  ],   ['fcm' => Fcm::PACKAGE_TWIC_APP, 'mail' => false] );

        }
        else{
            $m_contact = $res_contact->current();
            $m_contact->setRequestDate($date)
                      ->setDeletedDate(new IsNull('deleted_date'));
            $ret = $this->getMapper()->update($m_contact);
        }
        $this->getServiceSubscription()->add('PU'.$user, $user_id);
        return $ret;

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
    public function unfollow($user)
    {
          $identity = $this->getServiceUser()->getIdentity();
          $user_id = $identity['id'];
          $date = (new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s');



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

        $this->getServiceEvent()->sendData($user_id, 'user.unfollow', ['SU'.$user]);
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

        foreach ($res_contact as $m_contact) {
            $ret[] = $m_contact->getContactId();
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
     * @param int $user_id
     * @param string $search
     * @param array $filters
     *
     * @return \Dal\Db\ResultSet\ResultSet
     */
    public function getListFollowersId($user_id, $search = null, $filter = null)
    {
        $mapper = $this->getMapper();
        $res_contact = $mapper->usePaginator($filter)
                            ->getListFollowers(
                                  $user_id,
                                  $search,
                                  isset($filter) && isset($filter['order']) ? $filter['order'] : null
                              );

        $ret = [];

        foreach ($res_contact as $m_contact) {
            $ret[] = $m_contact->getUserId();
        }

        return null !== $filter ? ['list' => $ret, 'count' => $mapper->count()] : $ret;
    }

    /**
     * Get list contact id by users.
     *
     * @invokable
     *
     * @param int|array $user_id
     *
     * @return \Dal\Db\ResultSet\ResultSet
     */
    public function getListFollowingsId($user_id, $search = null, $filter = null)
    {
        $mapper = $this->getMapper();
        $res_contact = $mapper->usePaginator($filter)->getListFollowings(
              $user_id,
              $search,
              isset($filter) && isset($filter['order']) ? $filter['order'] : null);

        $ret = [];
        foreach ($res_contact as $m_contact) {
            $ret[] = $m_contact->getContactId();
        }

        return null !== $filter ? ['list' => $ret, 'count' => $mapper->count()] : $ret;
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
      * @param int $date_offset
      *
      * @return array
      */
    public function getRequestsCount( $start_date = null, $end_date = null, $interval_date = 'D',  $page_id  = null, $date_offset = 0)
    {

        $interval = $this->getServiceActivity()->interval($interval_date);
        $identity = $this->getServiceUser()->getIdentity();

        return $this->getMapper()->getRequestsCount($identity['id'], $interval, $start_date, $end_date, $page_id, $date_offset);
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
      *@param int $date_offset
      *
      * @return array
      */
    public function getAcceptedCount( $start_date = null, $end_date = null, $interval_date = 'D',  $page_id  = null, $date_offset = 0)
    {

        $interval = $this->getServiceActivity()->interval($interval_date);
        $identity = $this->getServiceUser()->getIdentity();

        return $this->getMapper()->getAcceptedCount($identity['id'], $interval, $start_date, $end_date, $page_id, $date_offset);
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
     * Get Service Page
     *
     * @return \Application\Service\Page
     */
    private function getServicePage()
    {
        return $this->container->get('app_service_page');
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

    /**
     * Get Service Fcm
     *
     * @return \Application\Service\Fcm
     */
    private function getServiceFcm()
    {
        return $this->container->get('fcm');
    }
}

<?php

namespace Application\Service;

use Dal\Service\AbstractService;
use Application\Model\Conversation as ModelConversation;
use ZendService\Google\Gcm\Notification as GcmNotification;


class Message extends AbstractService
{
    private static $id = 0;

    /**
     * Send message generique.
     *
     * @invokable
     *
     * @param string    $text
     * @param string    $library
     * @param int|array $to
     * @param int       $conversation_id
     *
     * @throws \Exception
     *
     * @return \Application\Model\MessageUser
     */
    public function send($text = null, $library = null, $to = null, $conversation_id = null)
    {
        $user_id = $this->getServiceUser()->getIdentity()['id'];

        if (null !== $to && $conversation_id === null) {
            if (!is_array($to)) {
                $to = [$to];
            }
            if (!in_array($user_id, $to)) {
                $to[] = $user_id;
            }
            $conversation_id = $this->getServiceConversationUser()->getConversationIDByUser($to);
            if ($conversation_id === false) {
                $conversation_id = $this->getServiceConversation()->_create(ModelConversation::TYPE_CHAT, $to);
            }
        } elseif ($conversation_id !== null) {
            
            $m_item = $this->getServiceItem()->getLite(null, $conversation_id)->current();
            if($m_item === false){
                if (!$this->getServiceConversation()->isInConversation($conversation_id, $user_id)) {
                    throw new \Exception('User '.$user_id.' is not in conversation '.$conversation_id);
                }
            }
            else{
                $ar_uid = ($m_item->getParticipants() === 'all') ?
                    $this->getServicePageUser()->getListByPage($m_item->getPageId())[$m_item->getPageId()] :
                    $this->getServiceItemUser()->getListUserId(null, $m_item->getId());
                if (!in_array($user_id, $ar_uid) && !$this->getServicePage()->isAdmin($m_item->getPageId())) {
                    throw new \Exception('User '.$user_id.' is not in conversation '.$conversation_id);
                }
            }
           
        }

        if (empty($text) && empty($library)) {
            throw new \Exception('error content && document are empty');
        }

        $library_id = (is_array($library)) ? $this->getServiceLibrary()->_add($library)->getId() : null;

        $m_message = $this->getModel()
            ->setText($text)
            ->setLibraryId($library_id)
            ->setUserId($user_id)
            ->setConversationId($conversation_id)
            ->setCreatedDate((new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s'));

        if ($this->getMapper()->insert($m_message) <= 0) {
            throw new \Exception('error insert message'); //@codeCoverageIgnore
        }

        $id = $this->getMapper()->getLastInsertValue();
        $type = $this->getServiceConversation()->getLite($conversation_id)->getType();
        if ($type === ModelConversation::TYPE_CHAT) {
            $message_user_id = $this->getServiceMessageUser()->send($id, $conversation_id, $text, $library);
        }
        
        if($type === ModelConversation::TYPE_LIVECLASS) {
            $m_item = $this->getServiceItem()->getLite(null, $conversation_id)->current();
            $page_id = $m_item->getPageId();
            if($m_item->getParticipants() === 'all') {
                $to = $this->getServicePageUser()->getListByPage($page_id)[$page_id];
            }else {
                $to = $this->getServiceItemUser()->getListUserId(null, $m_item->getId());
            }
        } else {
            $to = $this->getServiceConversationUser()->getListUserIdByConversation($conversation_id);
        }
        //marque la conversation no read
        $this->getServiceConversationUser()->noread($conversation_id, $id);

        //////////////////////// NODEJS //////////////////////////////:
        $this->sendMessage(
            [
            'conversation_id' => (int)$conversation_id,
            'id' => (int)$id,
            'users' => $to,
            'type' => $type,
            ]
        );

        //////////////////// USER //////////////////////////////////
        $res_user = $this->getServiceUser()->getLite($to);
        $ar_name = [];
        foreach ($res_user as $m_user) {
            $ar_name[$m_user->getId()] = substr($m_user->getFirstname(),0,1).'. '.$m_user->getLastname();
        }
        foreach ($to as $user) {
            ////////////////////// DOCUMENT /////////////////////////////
            $docs = [];
            if ($user_id != $user) {
                $gcm_notification = new GcmNotification();
                $tmp_ar_name = $ar_name;
                unset($tmp_ar_name[$user]);
                $gcm_notification->setTitle(implode(", ", $tmp_ar_name))
                    ->setSound("default")
                    ->setColor("#00A38B")
                    ->setIcon("icon")
                    ->setTag("CONV".$conversation_id)
                    ->setBody(((count($to) > 2)? explode(' ', $ar_name[$user_id])[0] . ": ":"").(empty($text)?"shared a file.":$text));
                
                
                $this->getServiceFcm()->send(
                    $user,
                    ['data' => [
                        'type' => 'message',
                        'data' => ['users' => $to,
                            'from' => $user_id,
                            'conversation' => (int)$conversation_id,
                            'message' =>  (int)$id,
                            'text' => $text,
                            'doc' => 'document'
                        ],
                    ]],
                    $gcm_notification,
                    Fcm::PACKAGE_TWIC_MESSENGER
                );
            }
        }
        
        return [
        'message_id' => $id,
        'conversation_id' => $conversation_id,
        'to' => $to,
        ];
    }

    /**
     * Send Message Node message.publish
     *
     * @param string $data
     */
    public function sendMessage($data)
    {
        $rep = false;
        try {
            $rep = $this->getServiceEvent()->nodeRequest('message.publish', $data);
            if ($rep->isError()) {
                throw new \Exception('Error jrpc nodeJs: ' . $rep->getError()->getMessage(), $rep->getError()->getCode());
            }
        } catch (\Exception $e) {
            syslog(1, 'Request message.publish : ' . json_encode($data));
            syslog(1, $e->getMessage());
        }

        return $rep;
    }

    /**
     * Get List By user Conversation
     *
     * @invokable
     *
     * @param int   $conversation_id
     * @param array $filter
     */
    public function getList($conversation_id, $filter = [])
    {
        $user_id = $this->getServiceUser()->getIdentity()['id'];
        $mapper = $this->getMapper();
        $res_message = $mapper->usePaginator($filter)->getList($user_id, $conversation_id);

        return [
        'list' => $res_message,
        'count' => $mapper->count()
        ];
    }

    /**
     * Get By user Message
     *
     * @param int $id
     */
    public function get($id)
    {
        $user_id = $this->getServiceUser()->getIdentity()['id'];

        return  $this->getMapper()->getList($user_id, null, $id)->current();
    }
    
    

    
     /**
      * Get page counts.
      *
      * @invokable
      *
      * @param string       $start_date
      * @param string       $end_date
      * @param string       $interval_date
      * @param array|string $type
      * @param int|array $page_id
      * @param int $date_offset
      *
      * @return array
      */
    public function getCount( $start_date = null, $end_date = null, $interval_date = 'D', $type = null, $page_id  = null, $date_offset = 0)
    {
        
        if(null !== $page_id && !is_array($page_id)){
            $page_id = [$page_id];
        }
        $interval = $this->getServiceActivity()->interval($interval_date);
        $identity = $this->getServiceUser()->getIdentity();
        
        return $this->getMapper()->getCount($identity['id'], $interval, $start_date, $end_date, $page_id, $type, $date_offset);
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
     * Get Service ConversationUser.
     *
     * @return \Application\Service\ConversationUser
     */
    private function getServiceConversationUser()
    {
        return $this->container->get('app_service_conversation_user');
    }

    /**
     * Get Service Message User
     *
     * @return \Application\Service\MessageUser
     */
    private function getServiceMessageUser()
    {
        return $this->container->get('app_service_message_user');
    }

    /**
     * Get Service Conversation
     *
     * @return \Application\Service\Conversation
     */
    private function getServiceConversation()
    {
        return $this->container->get('app_service_conversation');
    }

    /**
     * Get Service Item
     *
     * @return \Application\Service\Item
     */
    private function getServiceItem()
    {
        return $this->container->get('app_service_item');
    }
    
    /**
     * Get Service ItemUser
     *
     * @return \Application\Service\ItemUser
     */
    private function getServiceItemUser()
    {
        return $this->container->get('app_service_item_user');
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
     * Get Service PageUser
     *
     * @return \Application\Service\PageUser
     */
    private function getServicePageUser()
    {
        return $this->container->get('app_service_page_user');
    }
    
    /**
     * Get Service Library
     *
     * @return \Application\Service\Library
     */
    private function getServiceLibrary()
    {
        return $this->container->get('app_service_library');
    }
    
    /**
     * Get Service Service Conversation User.
     *
     * @return \Application\Service\Fcm
     */
    private function getServiceFcm()
    {
        return $this->container->get('fcm');
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
     * Get Service Event
     *
     * @return \Application\Service\Event
     */
    private function getServiceEvent()
    {
        return $this->container->get('app_service_event');
    }
}

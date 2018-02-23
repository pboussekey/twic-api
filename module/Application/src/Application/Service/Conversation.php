<?php

namespace Application\Service;

use Dal\Service\AbstractService;
use Application\Model\Conversation as ModelConversation;
use OpenTok\MediaMode;
use OpenTok\Role as OpenTokRole;
use Zend\Db\Sql\Predicate\IsNull;
use Application\Model\PageUser as ModelPageUser;

class Conversation extends AbstractService
{
    /**
     * Create New Conversation
     *
     * @invokable
     *
     * @param array $users
     *
     * @throws \Exception
     *
     * @return int
     */
    public function create($users = null)
    {
        return $this->_create(ModelConversation::TYPE_CHAT, $users);
    }

    public function _create($type = ModelConversation::TYPE_CHAT, $users = null, $has_video = null, $name = null)
    {
        $m_conversation = $this->getModel()
            ->setCreatedDate((new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s'))
            ->setName($name)
            ->setType($type);

        if ($this->getMapper()->insert($m_conversation) <= 0) {
            throw new \Exception('Error create conversation');// @codeCoverageIgnore
        }

        $conversation_id = $this->getMapper()->getLastInsertValue();

        if ($has_video === true) {
            $this->addVideo($conversation_id);
        }
        if (null !== $users) {
            $this->getServiceConversationUser()->add($conversation_id, $users);
        }

        return $conversation_id;
    }

    public function update($id, $name)
    {
        $m_conversation = $this->getModel()
            ->setId($id)
            ->setName($name);

        return $this->getMapper()->update($m_conversation);
    }

    /**
     * Get Conversation
     *
     * @invokable
     *
     * @param int|array
     */
    public function get($id)
    {
        $user_id = $this->getServiceUser()->getIdentity()['id'];

        $res_conversation = $this->getMapper()->getId($user_id, null, null, null, null, $id);
        foreach ($res_conversation as $m_conversation) {
            $cid = $m_conversation->getId();
            $message_id = $m_conversation->getMessage()->getId();
            if (is_numeric($message_id)) {
                $m_conversation->setMessage($this->getServiceMessage()->get($message_id));
            }

            $ar_uid = null;
            if($m_conversation->getType() === ModelConversation::TYPE_LIVECLASS) {
                $m_item = $this->getServiceItem()->getLite(null, $cid)->current();
                $ar_uid = ($m_item->getParticipants() === 'all') ?
                    $this->getServicePageUser()->getListByPage($m_item->getPageId())[$m_item->getPageId()] :
                    $this->getServiceItemUser()->getListUserId(null, $m_item->getId());
            } else {
                $ar_uid = $this->getServiceConversationUser()->getListUserIdByConversation($cid);
            }
            $m_conversation->setUsers($ar_uid);
            
            $m_page = $this->getServicePage()->getByConversationId($cid);
            if ($m_page) {
                $role = $this->getServicePageUser()->getRole($m_page->getId());
                if ($role) {
                    $m_conversation->setRole($role->getRole());
                }
            }
            //TYPE 2 => CHAT   ::: TYPE 1 => CHANNEL
            if ($m_conversation->getType() === ModelConversation::TYPE_CHAT) {
                $m_conversation->setOptions(
                    [
                      "record" => false,
                      "nb_user_autorecord" => 0,
                      "rules" => [
                          "autoPublishCamera"     => true,
                          "autoPublishMicrophone" => false,
                          "archive"               => false,
                          "raiseHand"             => false,
                          "publish"               => true,
                          "askDevice"             => false,
                          "askScreen"             => false,
                          "forceMute"             => false,
                          "forceUnpublish"        => false,
                          "kick"                  => false 
                      ]
                    ]
                );               
            } elseif ($m_conversation->getType() === ModelConversation::TYPE_LIVECLASS) {
                $m_conversation->setOptions(
                    [
                    "record" => false,
                    "nb_user_autorecord" => 2,
                    "rules" => [
                        "autoPublishCamera"       => true,
                        //"autoPublishCamera"       => [["roles" => ["admin"]]],
                        "autoPublishMicrophone"   => false,
                        "archive"                 => [["roles" => ["admin"]]],
                        "raiseHand"               => [["roles" => ["user"]]],
                        "publish"                 => [["roles" => ["admin"]]],
                        "askDevice"               => [["roles" => ["admin"]]],
                        "askScreen"               => [["roles" => ["admin"]]],
                        "forceMute"               => [["roles" => ["admin"]]],
                        "forceUnpublish"          => [["roles" => ["admin"]]],
                        "kick"                    => [["roles" => ["admin"]]],
                    ]
                    ]
                );
            }
        }


        $res_conversation->rewind();

        return (is_array($id)) ?
            $res_conversation->toArray(['id']) :
            $res_conversation->current();
    }

     /**
     * Get Conversation
     *
     * @invokable
     *
     * @param int|array
     */
    public function getReadDates($id)
    {
        if(!is_array($id)){
            $id = [$id];
        }
        $me = $this->getServiceUser()->getIdentity()['id'];
        $res_conversation_user = $this->getServiceConversationUser()->getListByConversation($id);
        $res = [];
        foreach($id as $i){
            $res[$i] = [];
        }
        foreach($res_conversation_user as $m_conversation_user){
            if($me !== $m_conversation_user->getUserId()){
                $res[$m_conversation_user->getConversationId()][$m_conversation_user->getUserId()] = 
                    $m_conversation_user->getLastMessage() instanceof IsNull ? 
                        null : 
                        $m_conversation_user->getLastMessage();
            }
        }
        return $res;        
    }
    
    /**
     * Get Conversation Unread
     *
     * @invokable
     *
     * @param bool   $contact
     * @param bool   $noread
     * @param int    $type
     * @param array  $filter
     * @param string $search
     */
    public function getList($contact = null, $noread = null, $type = null, $filter = null, $search = null)
    {
        $user_id = $this->getServiceUser()->getIdentity()['id'];
        $mapper = $this->getMapper();
        $res_conversation = $mapper->usePaginator($filter)->getId($user_id, $contact, $noread, $type, $search);
        foreach ($res_conversation as $m_conversation) {
            if ($m_conversation->getType() !==  ModelConversation::TYPE_CHANNEL && $m_conversation->getType() !==  ModelConversation::TYPE_LIVECLASS) {
                $m_conversation->setUsers($this->getServiceConversationUser()->getListUserIdByConversation($m_conversation->getId()));
            } 
        }

        $res_conversation->rewind();

        return (null === $filter) ? $res_conversation : [
            'list' => $res_conversation,
            'count' => $mapper->count()
          ];
    }

    /**
     * Add video Token in conversaton if not exist.
     *
     * @invokable
     *
     * @param int $id
     *
     * @return string
     */
    public function addVideo($id)
    {
        $m_conversation = $this->getMapper()->select($this->getModel()->setId($id))->current();
        $token = $m_conversation->getToken();
        $media_mode = ($m_conversation->getType() === ModelConversation::TYPE_CHAT) ?
            MediaMode::RELAYED :
            MediaMode::ROUTED;

        if ($token === null || $token instanceof IsNull) {
            $token = $this->getServiceZOpenTok()->getSessionId($media_mode);
            $this->getMapper()->update($this->getModel()->setToken($token), ['id' => $id, new IsNull('token')]);
        }

        return $token;
    }

    /**
     * Get Token video Token User in conversaton if not exist.
     *
     * @invokable
     *
     * @param int $id
     *
     * @return int
     */
    public function getToken($id)
    {
        $user_id = $this->getServiceUser()->getIdentity()['id'];
        $token = $this->addVideo($id);

        $page_id = null;
        $is_admin = false;
        $res_item = $this->getServiceItem()->getLite(null, $id);
        if($res_item->count() > 0) {
            $page_id = $res_item->current()->getPageId();
        } else {
            $m_page = $this->getServicePage()->getLite(null, $id);
            if($m_page) {
                $page_id = $m_page->getId();
            }
        }
        
        if(null !== $page_id) {
            $ar_pu = $this->getServicePageUser()->getListByPage($page_id, ModelPageUser::ROLE_ADMIN);
            $is_admin = (in_array($user_id, $ar_pu[$page_id]));
        }
            
        return [
            'token' => $this->getServiceZOpenTok()->createToken($token, '{"id":' . $user_id . '}', ($is_admin ? OpenTokRole::MODERATOR: OpenTokRole::PUBLISHER)),
            'session' => $token,
            'role' => $is_admin ? 'admin':'user'
        ];
    }

    /**
     * Get Id conversation By user(s)
     *
     * @invokable
     *
     * @param int|array $user
     * @param int|array $type
     *
     * @return int
     */
    public function getIdByUser($user_id, $type = 2)
    {
        if (!is_array($user_id)) {
            $user_id = [$user_id];
        }

        return $this->getServiceConversationUser()->getConversationIDByUser($user_id, $type);
    }

    /**
     * Get Conversation
     *
     * @return \Application\Model\Conversation
     */
    public function getLite($id)
    {
        $res_conversation = $this->getMapper()->select($this->getModel()->setId($id));

        return  $res_conversation->current();
    }

    /**
     * Mark Read Message(s).
     *
     * @invokable
     *
     * @param int|array $id
     *
     * @return int
     */
    public function read($id)
    {
        return $this->getServiceConversationUser()->read($id);
    }
    
     /**
      * Check If is in conversation.
      *
      * @param int $conversation_id
      * @param int $user_id
      *
      * @return bool
      */
    public function isInConversation($conversation_id, $user_id)
    {
        $m_conversation = $this->get($conversation_id);
       
        

        return in_array($user_id, $m_conversation->getUsers());
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
     * Get Service User.
     *
     * @return \Application\Service\User
     */
    private function getServiceUser()
    {
        return $this->container->get('app_service_user');
    }

    /**
     * Get Service Messsage.
     *
     * @return \Application\Service\Message
     */
    private function getServiceMessage()
    {
        return $this->container->get('app_service_message');
    }

    /**
     * Get Service Service OpenTok.
     *
     * @return \ZOpenTok\Service\OpenTok
     */
    private function getServiceZOpenTok()
    {
        return $this->container->get('opentok.service');
    }

    /**
     * Get Service Page User
     *
     * @return \Application\Service\PageUser
     */
    private function getServicePageUser()
    {
        return $this->container->get('app_service_page_user');
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
}

<?php

namespace Application\Model;

use Application\Model\Base\Conversation as BaseConversation;

class Conversation extends BaseConversation
{
    const TYPE_CHANNEL = 1;
    const TYPE_CHAT = 2;
    const TYPE_LIVECLASS = 3;

    const DEFAULT_NAME = 'Chat';

    protected $messages;
    protected $users;
    protected $message_user;
    protected $nb_unread;
    protected $nb_users;
    protected $message;
    protected $role;
    protected $page_id;
    protected $item_id;

    public function exchangeArray(array &$data)
    {
        parent::exchangeArray($data);

        $this->message = $this->requireModel('app_model_message', $data);
    }

    /**
     * @return int $item_id
     */
    public function getItemId()
    {
        return $this->item_id;
    }
    
    /**
     * @param int $itemId
     */
    public function setItemId($item_id)
    {
        $this->item_id = $item_id;
    }
    
    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    public function getMessages()
    {
        return $this->messages;
    }

    public function setMessages($messages)
    {
        $this->messages = $messages;

        return $this;
    }

    public function getMessageUser()
    {
        return $this->message_user;
    }

    public function setMessageUser($message_user)
    {
        $this->message_user = $message_user;

        return $this;
    }

    public function getUsers()
    {
        return $this->users;
    }

    public function setUsers($users)
    {
        $this->users = $users;

        return $this;
    }

    public function getNbUnread()
    {
        return $this->nb_unread;
    }

    public function setNbUnread($nb_unread)
    {
        $this->nb_unread = $nb_unread;

        return $this;
    }

    /**
     * Get the value of Nb Users
     *
     * @return mixed
     */
    public function getNbUsers()
    {
        return $this->nb_users;
    }

    /**
     * Set the value of Nb Users
     *
     * @param mixed nb_users
     *
     * @return self
     */
    public function setNbUsers($nb_users)
    {
        $this->nb_users = $nb_users;

        return $this;
    }

    /**
     * Get the value of Role
     *
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set the value of Role
     *
     * @param mixed role
     *
     * @return self
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }


    /**
     * Get the value of Page Id
     *
     * @return mixed
     */
    public function getPageId()
    {
        return $this->page_id;
    }

    /**
     * Set the value of Page Id
     *
     * @param mixed page_id
     *
     * @return self
     */
    public function setPageId($page_id)
    {
        $this->page_id = $page_id;

        return $this;
    }
}

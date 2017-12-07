<?php

namespace Application\Model;

use Application\Model\Base\Message as BaseMessage;

class Message extends BaseMessage
{
    protected $message_user;
    protected $library;
    protected $count;

    public function exchangeArray(array &$data)
    {
        parent::exchangeArray($data);

        $this->message_user = $this->requireModel('app_model_message_user', $data);
        $this->library = $this->requireModel('app_model_library', $data);
    }

    /**
     * Get the value of Message User
     *
     * @return mixed
     */
    public function getMessageUser()
    {
        return $this->message_user;
    }

    /**
     * Set the value of Message User
     *
     * @param mixed message_user
     *
     * @return self
     */
    public function setMessageUser($message_user)
    {
        $this->message_user = $message_user;

        return $this;
    }

    /**
     * Get the value of Library
     *
     * @return mixed
     */
    public function getLibrary()
    {
        return $this->library;
    }

    /**
     * Set the value of Library
     *
     * @param mixed library
     *
     * @return self
     */
    public function setLibrary($library)
    {
        $this->library = $library;

        return $this;
    }
    
      /**
     * Get count
     *
     * @return mixed
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Set the value of Count
     *
     * @param mixed count
     *
     * @return self
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }
}

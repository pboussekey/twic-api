<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class MessageUser extends AbstractModel
{
    protected $id;
    protected $message_id;
    protected $conversation_id;
    protected $from_id;
    protected $user_id;
    protected $read_date;
    protected $deleted_date;
    protected $created_date;
    protected $type;

    protected $prefix = 'message_user';

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getMessageId()
    {
        return $this->message_id;
    }

    public function setMessageId($message_id)
    {
        $this->message_id = $message_id;

        return $this;
    }

    public function getConversationId()
    {
        return $this->conversation_id;
    }

    public function setConversationId($conversation_id)
    {
        $this->conversation_id = $conversation_id;

        return $this;
    }

    public function getFromId()
    {
        return $this->from_id;
    }

    public function setFromId($from_id)
    {
        $this->from_id = $from_id;

        return $this;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getReadDate()
    {
        return $this->read_date;
    }

    public function setReadDate($read_date)
    {
        $this->read_date = $read_date;

        return $this;
    }

    public function getDeletedDate()
    {
        return $this->deleted_date;
    }

    public function setDeletedDate($deleted_date)
    {
        $this->deleted_date = $deleted_date;

        return $this;
    }

    public function getCreatedDate()
    {
        return $this->created_date;
    }

    public function setCreatedDate($created_date)
    {
        $this->created_date = $created_date;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }
}

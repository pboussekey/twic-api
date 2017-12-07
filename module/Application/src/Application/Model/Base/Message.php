<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class Message extends AbstractModel
{
    protected $id;
    protected $title;
    protected $text;
    protected $library_id;
    protected $is_draft;
    protected $type;
    protected $conversation_id;
    protected $created_date;
    protected $user_id;

    protected $prefix = 'message';

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    public function getLibraryId()
    {
        return $this->library_id;
    }

    public function setLibraryId($library_id)
    {
        $this->library_id = $library_id;

        return $this;
    }

    public function getIsDraft()
    {
        return $this->is_draft;
    }

    public function setIsDraft($is_draft)
    {
        $this->is_draft = $is_draft;

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

    public function getConversationId()
    {
        return $this->conversation_id;
    }

    public function setConversationId($conversation_id)
    {
        $this->conversation_id = $conversation_id;

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

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }
}

<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class Library extends AbstractModel
{
    protected $id;
    protected $name;
    protected $link;
    protected $token;
    protected $type;
    protected $created_date;
    protected $deleted_date;
    protected $updated_date;
    protected $folder_id;
    protected $owner_id;
    protected $box_id;
    protected $global;
    protected $text;

    protected $prefix = 'library';

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setToken($token)
    {
        $this->token = $token;

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

    public function getCreatedDate()
    {
        return $this->created_date;
    }

    public function setCreatedDate($created_date)
    {
        $this->created_date = $created_date;

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

    public function getUpdatedDate()
    {
        return $this->updated_date;
    }

    public function setUpdatedDate($updated_date)
    {
        $this->updated_date = $updated_date;

        return $this;
    }

    public function getFolderId()
    {
        return $this->folder_id;
    }

    public function setFolderId($folder_id)
    {
        $this->folder_id = $folder_id;

        return $this;
    }

    public function getOwnerId()
    {
        return $this->owner_id;
    }

    public function setOwnerId($owner_id)
    {
        $this->owner_id = $owner_id;

        return $this;
    }

    public function getBoxId()
    {
        return $this->box_id;
    }

    public function setBoxId($box_id)
    {
        $this->box_id = $box_id;

        return $this;
    }

    public function getGlobal()
    {
        return $this->global;
    }

    public function setGlobal($global)
    {
        $this->global = $global;

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
}

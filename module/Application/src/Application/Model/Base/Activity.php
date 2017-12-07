<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class Activity extends AbstractModel
{
    protected $id;
    protected $event;
    protected $object_id;
    protected $object_name;
    protected $object_value;
    protected $object_data;
    protected $target_id;
    protected $target_name;
    protected $target_data;
    protected $date;
    protected $user_id;

    protected $prefix = 'activity';

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getEvent()
    {
        return $this->event;
    }

    public function setEvent($event)
    {
        $this->event = $event;

        return $this;
    }

    public function getObjectId()
    {
        return $this->object_id;
    }

    public function setObjectId($object_id)
    {
        $this->object_id = $object_id;

        return $this;
    }

    public function getObjectName()
    {
        return $this->object_name;
    }

    public function setObjectName($object_name)
    {
        $this->object_name = $object_name;

        return $this;
    }

    public function getObjectValue()
    {
        return $this->object_value;
    }

    public function setObjectValue($object_value)
    {
        $this->object_value = $object_value;

        return $this;
    }

    public function getObjectData()
    {
        return $this->object_data;
    }

    public function setObjectData($object_data)
    {
        $this->object_data = $object_data;

        return $this;
    }

    public function getTargetId()
    {
        return $this->target_id;
    }

    public function setTargetId($target_id)
    {
        $this->target_id = $target_id;

        return $this;
    }

    public function getTargetName()
    {
        return $this->target_name;
    }

    public function setTargetName($target_name)
    {
        $this->target_name = $target_name;

        return $this;
    }

    public function getTargetData()
    {
        return $this->target_data;
    }

    public function setTargetData($target_data)
    {
        $this->target_data = $target_data;

        return $this;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date)
    {
        $this->date = $date;

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

<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class Set extends AbstractModel
{
    protected $id;
    protected $uid;
    protected $name;
    protected $course_id;
    protected $is_used;

    protected $prefix = 'set';

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getUid()
    {
        return $this->uid;
    }

    public function setUid($uid)
    {
        $this->uid = $uid;

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

    public function getCourseId()
    {
        return $this->course_id;
    }

    public function setCourseId($course_id)
    {
        $this->course_id = $course_id;

        return $this;
    }

    public function getIsUsed()
    {
        return $this->is_used;
    }

    public function setIsUsed($is_used)
    {
        $this->is_used = $is_used;

        return $this;
    }
}

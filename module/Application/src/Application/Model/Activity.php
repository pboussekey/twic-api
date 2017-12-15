<?php

namespace Application\Model;

use Application\Model\Base\Activity as BaseActivity;

class Activity extends BaseActivity
{
    protected $linkedin_id;
    protected $object_name;
    protected $count;
    protected $min_date;
    protected $max_date;

    public function setObjectName($object_name)
    {
        $this->object_name = $object_name;

        return $this;
    }

    public function getObjectName()
    {
        return $this->object_name;
    }

    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    public function getCount()
    {
        return $this->count;
    }
    
    public function getLinkedinId()
    {
        return $this->linkedin_id;
    }

    public function setLinkedinId($linkedin_id)
    {
        $this->linkedin_id = $linkedin_id;

        return $this;
    }

    public function setMinDate($min_date)
    {
        $this->min_date = $min_date;

        return $this;
    }

    public function getMinDate()
    {
        return $this->min_date;
    }

    public function setMaxDate($max_date)
    {
        $this->max_date = $max_date;

        return $this;
    }

    public function getMaxDate()
    {
        return $this->max_date;
    }
}

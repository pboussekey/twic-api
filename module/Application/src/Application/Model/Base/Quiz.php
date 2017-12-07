<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class Quiz extends AbstractModel
{
    protected $id;
    protected $name;
    protected $attempt_count;
    protected $time_limit;
    protected $created_date;
    protected $item_id;
    protected $user_id;

    protected $prefix = 'quiz';

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

    public function getAttemptCount()
    {
        return $this->attempt_count;
    }

    public function setAttemptCount($attempt_count)
    {
        $this->attempt_count = $attempt_count;

        return $this;
    }

    public function getTimeLimit()
    {
        return $this->time_limit;
    }

    public function setTimeLimit($time_limit)
    {
        $this->time_limit = $time_limit;

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

    public function getItemId()
    {
        return $this->item_id;
    }

    public function setItemId($item_id)
    {
        $this->item_id = $item_id;

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

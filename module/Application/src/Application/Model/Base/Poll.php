<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class Poll extends AbstractModel
{
    protected $id;
    protected $title;
    protected $expiration_date;
    protected $time_limit;
    protected $attempt_count;
    protected $item_id;

    protected $prefix = 'poll';

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

    public function getExpirationDate()
    {
        return $this->expiration_date;
    }

    public function setExpirationDate($expiration_date)
    {
        $this->expiration_date = $expiration_date;

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

    public function getAttemptCount()
    {
        return $this->attempt_count;
    }

    public function setAttemptCount($attempt_count)
    {
        $this->attempt_count = $attempt_count;

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
}

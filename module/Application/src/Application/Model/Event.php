<?php

namespace Application\Model;

use Application\Model\Base\Event as BaseEvent;

class Event extends BaseEvent
{
    protected $read_date;

    public function getReadDate()
    {
        return $this->read_date;
    }

    public function setReadDate($read_date)
    {
        $this->read_date = $read_date;

        return $this;
    }

}

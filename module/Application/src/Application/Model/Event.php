<?php

namespace Application\Model;

use Application\Model\Base\Event as BaseEvent;

class Event extends BaseEvent
{
    protected $read_date;
    protected $text;
    protected $picture;

    public function getReadDate()
    {
        return $this->read_date;
    }

    public function setReadDate($read_date)
    {
        $this->read_date = $read_date;

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

    public function getPicture()
    {
        return $this->picture;
    }

    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

}

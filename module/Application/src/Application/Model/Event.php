<?php

namespace Application\Model;

use Application\Model\Base\Event as BaseEvent;

class Event extends BaseEvent
{
    protected $count;
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

    public function getCount()
    {
        return $this->count;
    }

    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

}

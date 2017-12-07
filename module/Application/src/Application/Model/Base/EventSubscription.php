<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class EventSubscription extends AbstractModel
{
    protected $libelle;
    protected $event_id;

    protected $prefix = 'event_subscription';

    public function getLibelle()
    {
        return $this->libelle;
    }

    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getEventId()
    {
        return $this->event_id;
    }

    public function setEventId($event_id)
    {
        $this->event_id = $event_id;

        return $this;
    }
}

<?php

namespace Application\Service;

use Dal\Service\AbstractService;

class EventSubscription extends AbstractService
{
    /**
     * Add Event Subscription
     *
     * @param string $libelle
     * @param int    $event_id
     */
    public function add($libelle, $event_id)
    {
        $libelle = array_unique($libelle);
        $m_event_subscription = $this->getModel()->setEventId($event_id);
        foreach ($libelle as $l) {
            $this->getMapper()->insert($m_event_subscription->setLibelle($l));
        }
    }
}

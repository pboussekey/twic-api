<?php

namespace Mail\Template\Storage;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManager;
use Zend\ServiceManager\ServiceManager;

abstract class AbstractStorage implements StorageInterface
{
    /**
     * @var ServiceManager
     */
    protected $servicemanager;

    /**
     * (non-PHPdoc).
     *
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        if (null === $this->events) {
            $this->setEventManager(new EventManager());
        }

        return $this->events;
    }
}

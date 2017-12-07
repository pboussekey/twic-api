<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class GcmGroup extends AbstractModel
{
    protected $id;
    protected $notification_key_name;
    protected $notification_key;

    protected $prefix = 'gcm_group';

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getNotificationKeyName()
    {
        return $this->notification_key_name;
    }

    public function setNotificationKeyName($notification_key_name)
    {
        $this->notification_key_name = $notification_key_name;

        return $this;
    }

    public function getNotificationKey()
    {
        return $this->notification_key;
    }

    public function setNotificationKey($notification_key)
    {
        $this->notification_key = $notification_key;

        return $this;
    }
}

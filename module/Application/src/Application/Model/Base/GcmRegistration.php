<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class GcmRegistration extends AbstractModel
{
    protected $gcm_group_id;
    protected $registration_id;
    protected $uuid;

    protected $prefix = 'gcm_registration';

    public function getGcmGroupId()
    {
        return $this->gcm_group_id;
    }

    public function setGcmGroupId($gcm_group_id)
    {
        $this->gcm_group_id = $gcm_group_id;

        return $this;
    }

    public function getRegistrationId()
    {
        return $this->registration_id;
    }

    public function setRegistrationId($registration_id)
    {
        $this->registration_id = $registration_id;

        return $this;
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }
}

<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class CircleOrganization extends AbstractModel
{
    protected $circle_id;
    protected $organization_id;

    protected $prefix = 'circle_organization';

    public function getCircleId()
    {
        return $this->circle_id;
    }

    public function setCircleId($circle_id)
    {
        $this->circle_id = $circle_id;

        return $this;
    }

    public function getOrganizationId()
    {
        return $this->organization_id;
    }

    public function setOrganizationId($organization_id)
    {
        $this->organization_id = $organization_id;

        return $this;
    }
}

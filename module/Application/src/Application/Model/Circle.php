<?php

namespace Application\Model;

use Application\Model\Base\Circle as BaseCircle;

class Circle extends BaseCircle
{
    protected $organizations;

    public function setOrganizations($organizations)
    {
        $this->organizations = $organizations;

        return $this;
    }

    public function getOrganizations()
    {
        return $this->organizations;
    }
}

<?php

namespace Rbac\Db\Service;

use Dal\Service\AbstractService;

class RoleRelation extends AbstractService
{
    /**
     * @return \Dal\Db\ResultSet\ResultSet
     */
    public function getListByRole($role)
    {
        return $this->getMapper()->getListByRole($role);
    }
}

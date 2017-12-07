<?php

namespace Rbac\Db\Mapper;

use Dal\Mapper\AbstractMapper;

class RolePermission extends AbstractMapper
{
    public function getDroit()
    {
        $select = $this->tableGateway->getSql()->select();

        $select->columns(array('right'))
            ->join('rbac_role', 'rbac_role.id = rbac_droit.rbac_role_id', array('name', 'parent_id'), $select::JOIN_LEFT)
            ->join('rbac_permission', 'rbac_permission.id = rbac_droit.rbac_permission_id', array('name'), $select::JOIN_LEFT);

        return $this->selectWith($select);
    }
}

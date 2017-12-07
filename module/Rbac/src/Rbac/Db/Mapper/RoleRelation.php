<?php

namespace Rbac\Db\Mapper;

use Dal\Mapper\AbstractMapper;

class RoleRelation extends AbstractMapper
{
    public function getListByRole($role)
    {
        $select = $this->tableGateway->getSql()->select();

        $select->columns(array())
            ->join('role', 'role_relation.parent_id=role.id', array('id', 'name'))
            ->where(array('role_relation.role_id' => $role));

        return $this->selectWith($select);
    }
}

<?php

namespace Rbac\Db\Mapper;

use Dal\Mapper\AbstractMapper;

class Role extends AbstractMapper
{
    public function getListByParentId($parent)
    {
        $select = $this->tableGateway->getSql()->select();

        $select->columns(array('id', 'name'))
            ->join('role_relation', 'role_relation.parent_id=role.id', array())
            ->where(array('role_relation.role_id' => $parent));

        return $this->selectWith($select);
    }
}

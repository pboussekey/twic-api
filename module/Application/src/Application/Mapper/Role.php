<?php

namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;

class Role extends AbstractMapper
{
    public function getRoleByUser($user)
    {
        $select = $this->tableGateway->getSql()->select();

        $select->columns(array('id', 'name'))
            ->join('user_role', 'user_role.role_id=role.id', array())
            ->where(array('user_role.user_id' => $user));

        return $this->selectWith($select);
    }
}

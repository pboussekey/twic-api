<?php

namespace Rbac\Db\Mapper;

use Dal\Mapper\AbstractMapper;

class Permission extends AbstractMapper
{
    /**
     * @param int $role
     *
     * @return \Dal\Db\ResultSet\ResultSet
     */
    public function getListByRole($role)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(array('id', 'libelle'))
            ->join('role_permission', 'role_permission.permission_id =permission.id', array())
            ->where(array('role_permission.role_id' => $role));

        return $this->selectWith($select);
    }

    public function getList($filter = null, $roleId = null, $search = null)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(array('id', 'libelle'))
            ->join('role_permission', 'role_permission.permission_id=permission.id', array('id'), $select::JOIN_LEFT)
            ->join('role', 'role.id=role_permission.role_id', array('id', 'name'), $select::JOIN_LEFT);

        if (!empty($roleId)) {
            $select->where(array('role_permission.role_id' => $roleId));
        }

        if (!empty($search)) {
            $select->where(array('permission.libelle LIKE ? ' => '%'.$search.'%'));
        }

        $select->where('role.name != "guest"');

        return $this->selectWith($select);
    }
}

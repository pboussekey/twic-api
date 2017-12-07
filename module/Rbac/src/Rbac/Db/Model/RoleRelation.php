<?php

namespace Rbac\Db\Model;

use Rbac\Db\Model\Base\RoleRelation as BaseRoleRelation;

class RoleRelation extends BaseRoleRelation
{
    protected $role;
    protected $permission;

    public function exchangeArray(array &$data)
    {
        parent::exchangeArray($data);

        $this->permission = $this->requireModel('rbac_service_permission', $data);
        $this->role = $this->requireModel('rbac_service_role', $data);
    }

    public function setPermission($permission)
    {
        $this->permission = $permission;

        return $this;
    }

    public function getPermission()
    {
        return $this->permission;
    }

    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    public function getRole()
    {
        return $this->role;
    }
}

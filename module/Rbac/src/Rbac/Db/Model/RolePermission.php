<?php

namespace Rbac\Db\Model;

use Rbac\Db\Model\Base\RolePermission as BaseRolePermission;

class RolePermission extends BaseRolePermission
{
    protected $role;

    protected $permission;

    public function exchangeArray(array &$data)
    {
        parent::exchangeArray($data);

        $this->permission = $this->requireModel('rbac_service_permission', $data);
        $this->role = $this->requireModel('rbac_service_role', $data);
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    public function getPermission()
    {
        return $this->permission;
    }

    public function setPermission($permission)
    {
        $this->permission = $permission;

        return $this;
    }
}

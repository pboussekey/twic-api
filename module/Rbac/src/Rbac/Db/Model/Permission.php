<?php

namespace Rbac\Db\Model;

use Rbac\Db\Model\Base\Permission as BasePermission;

class Permission extends BasePermission
{
    protected $role;
    protected $role_permission;

    public function exchangeArray(array &$data)
    {
        if ($this->isRepeatRelational()) {
            return;
        }
        parent::exchangeArray($data);

        $this->role = $this->requireModel('rbac_model_role', $data);
        $this->role_permission = $this->requireModel('rbac_model_role_permission', $data);
    }

    public function getRolePermission()
    {
        return $this->role_permission;
    }

    public function setRolePermission($role_permission)
    {
        $this->role_permission = $role_permission;

        return $this;
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

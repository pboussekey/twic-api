<?php

namespace Rbac\Db\Model\Base;

use Dal\Model\AbstractModel;

class RolePermission extends AbstractModel
{
    protected $id;
    protected $role_id;
    protected $permission_id;
    protected $prefix = 'role_permission';

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function setRoleId($role_id)
    {
        $this->role_id = $role_id;

        return $this;
    }

    public function getRoleId()
    {
        return $this->role_id;
    }

    public function setPermissionId($permission_id)
    {
        $this->permission_id = $permission_id;

        return $this;
    }

    public function getPermissionId()
    {
        return $this->permission_id;
    }
}

<?php

namespace Rbac\Db\Model;

use Rbac\Db\Model\Base\Role as BaseRole;

class Role extends BaseRole
{
    protected $parent;
    protected $permission;

    public function setPermission($permission)
    {
        $this->permission = $permission;

        return $this;
    }

    public function getPermission()
    {
        return $this->permission;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    public function getParent()
    {
        return $this->parent;
    }
}

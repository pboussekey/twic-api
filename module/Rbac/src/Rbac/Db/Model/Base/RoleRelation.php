<?php

namespace Rbac\Db\Model\Base;

use Dal\Model\AbstractModel;

class RoleRelation extends AbstractModel
{
    protected $role_id;
    protected $parent_id;

    protected $prefix = 'role_relation';

    public function setRoleId($role_id)
    {
        $this->role_id = $role_id;

        return $this;
    }

    public function getRoleId()
    {
        return $this->role_id;
    }

    public function getParentId()
    {
        return $this->parent_id;
    }

    public function setParentId($parent_id)
    {
        $this->parent_id = $parent_id;

        return $this;
    }
}

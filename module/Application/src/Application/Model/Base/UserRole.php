<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class UserRole extends AbstractModel
{
    protected $user_id;
    protected $role_id;

    protected $prefix = 'user_role';

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getRoleId()
    {
        return $this->role_id;
    }

    public function setRoleId($role_id)
    {
        $this->role_id = $role_id;

        return $this;
    }
}

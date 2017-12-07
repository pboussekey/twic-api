<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class GroupUser extends AbstractModel
{
    protected $group_id;
    protected $user_id;

    protected $prefix = 'group_user';

    public function getGroupId()
    {
        return $this->group_id;
    }

    public function setGroupId($group_id)
    {
        $this->group_id = $group_id;

        return $this;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }
}

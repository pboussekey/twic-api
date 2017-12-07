<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class SetGroup extends AbstractModel
{
    protected $set_id;
    protected $group_id;

    protected $prefix = 'set_group';

    public function getSetId()
    {
        return $this->set_id;
    }

    public function setSetId($set_id)
    {
        $this->set_id = $set_id;

        return $this;
    }

    public function getGroupId()
    {
        return $this->group_id;
    }

    public function setGroupId($group_id)
    {
        $this->group_id = $group_id;

        return $this;
    }
}

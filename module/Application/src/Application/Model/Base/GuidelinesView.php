<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class GuidelinesView extends AbstractModel
{
    protected $state;
    protected $user_id;

    protected $prefix = 'guidelines_view';

    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;

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

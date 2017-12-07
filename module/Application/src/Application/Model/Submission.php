<?php

namespace Application\Model;

use Application\Model\Base\Submission as BaseSubmission;

class Submission extends BaseSubmission
{
    protected $item_users;

    /**
     * Get the value of Item Users
     *
     * @return mixed
     */
    public function getItemUsers()
    {
        return $this->item_users;
    }

    /**
     * Set the value of Item Users
     *
     * @param mixed item_users
     *
     * @return self
     */
    public function setItemUsers($item_users)
    {
        $this->item_users = $item_users;

        return $this;
    }
}

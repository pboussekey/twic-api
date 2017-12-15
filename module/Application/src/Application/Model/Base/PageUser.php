<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class PageUser extends AbstractModel
{
    protected $page_id;
    protected $user_id;
    protected $role;
    protected $state;
    protected $is_pinned;

    protected $prefix = 'page_user';

    public function getPageId()
    {
        return $this->page_id;
    }

    public function setPageId($page_id)
    {
        $this->page_id = $page_id;

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

    public function getRole()
    {
        return $this->role;
    }

    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    public function getIsPinned()
    {
        return $this->is_pinned;
    }

    public function setIsPinned($is_pinned)
    {
        $this->is_pinned = $is_pinned;

        return $this;
    }

}
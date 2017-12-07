<?php

namespace Application\Model;

use Application\Model\Base\PostLike as BasePostLike;

class PostLike extends BasePostLike
{
    
    protected $count;
    
    /**
     * Get count
     *
     * @return mixed
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Set the value of Count
     *
     * @param mixed count
     *
     * @return self
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }
}

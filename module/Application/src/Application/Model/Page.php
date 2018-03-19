<?php

namespace Application\Model;

use Address\Model\Address;
use Application\Model\Base\Page as BasePage;

class Page extends BasePage
{
    const TYPE_EVENT='event';
    const TYPE_GROUP='group';
    const TYPE_ORGANIZATION='organization';
    const TYPE_COURSE='course';

    const ADMISSION_FREE='free';
    const ADMISSION_OPEN='open';
    const ADMISSION_INVITATION='invitation';
    
    const CONFIDENTIALITY_PUBLIC=0;
    const CONFIDENTIALITY_CLOSED=1;
    const CONFIDENTIALITY_SECRET=2;

    protected $state;
    protected $role;
    protected $page_relation;
    protected $tags;
    protected $user;
    protected $address;
    protected $owner;

    
    protected $average;
    protected $median;
    protected $percentile;
    protected $count;
    protected $last_post;

    public function exchangeArray(array &$data)
    {
        parent::exchangeArray($data);
        
        $this->user = $this->requireModel('app_model_user', $data, 'p_user');
        $this->address = $this->requireModel('addr_model_address', $data);
        $this->page_relation = $this->requireModel('app_model_page_relation', $data);
    }
    
    public function setOwner($owner)
    {
        $this->owner = $owner;

        return $this;
    }

    public function getOwner()
    {
        return $this->owner;
    }
    

    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    public function getAddress()
    {
        return $this->address;
    }
    
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }
    
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    public function getTags()
    {
        return $this->tags;
    }
   
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    public function getState()
    {
        return $this->state;
    }
    
    public function setMedian($median)
    {
        $this->median = $median;

        return $this;
    }

    public function getMedian()
    {
        return $this->median;
    }
    
    public function setAverage($average)
    {
        $this->average = $average;

        return $this;
    }

    public function getAverage()
    {
        return $this->average;
    }

    
    public function setPercentile($percentile)
    {
        $this->percentile = $percentile;

        return $this;
    }

    public function getPercentile()
    {
        return $this->percentile;
    }
    
     /**
     * Get the value of Page Relation
     *
     * @return mixed
     */
    public function getPageRelation()
    {
        return $this->page_relation;
    }

    /**
     * Set the value of Page Relation
     *
     * @param mixed page_relation
     *
     * @return self
     */
    public function setPageRelation($page_relation)
    {
        $this->page_relation = $page_relation;

        return $this;
    }

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
    

    /**
     * Get last post date
     *
     * @return date
     */
    public function getLastPost()
    {
        return $this->last_post;
    }

    /**
     * Set the value of last post date
     *
     * @param date count
     *
     * @return self
     */
    public function setLastPost($last_post)
    {
        $this->last_post = $last_post;

        return $this;
    }
}

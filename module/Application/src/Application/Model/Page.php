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
    const CONFIDENTIALITY_PRIVATE=1;

    protected $tags;
    protected $users;
    protected $docs;
    protected $events;
    protected $state;
    protected $role;

    protected $user;
    protected $organization;
    protected $page;
    protected $owner;
    protected $address;
    protected $page_relation;
    
    protected $average;
    protected $median;
    protected $percentile;
    protected $count;

    public function exchangeArray(array &$data)
    {
        parent::exchangeArray($data);

        $this->user = $this->requireModel('app_model_user', $data, 'p_user');
        $this->organization = $this->requireModel('app_model_school', $data);
        //$this->page = $this->requireModel('app_model_page', $data);
        $this->address = $this->requireModel('addr_model_address', $data);
        $this->page_relation = $this->requireModel('app_model_page_relation', $data);
    }

    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    public function getAddress()
    {
        if ($this->address instanceof Address && $this->address->getId() === null) {
            $this->address = null;
        }

        return $this->address;
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function setOwner($owner)
    {
        $this->owner = $owner;

        return $this;
    }

    public function setOrganization($organization)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * @return \Application\Model\Page
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @return \Application\Model\Page
     */
    public function getPage()
    {
        return $this->page;
    }


    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return \Application\Model\User
     */
    public function getUser()
    {
        return $this->user;
    }

    public function setUsers($users)
    {
        $this->users = $users;

        return $this;
    }

    public function getUsers()
    {
        return $this->users;
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

    public function setDocs($docs)
    {
        $this->docs = $docs;

        return $this;
    }

    public function getDocs()
    {
        return $this->docs;
    }

    public function setEvents($events)
    {
        $this->events = $events;

        return $this;
    }

    public function getEvents()
    {
        return $this->events;
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
}

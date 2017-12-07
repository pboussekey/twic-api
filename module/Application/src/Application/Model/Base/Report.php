<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class Report extends AbstractModel
{
    protected $id;
    protected $reporter_id;
    protected $user_id;
    protected $post_id;
    protected $page_id;
    protected $created_date;
    protected $treatment_date;
    protected $treated;
    protected $reason;
    protected $description;
    protected $validate;

    protected $prefix = 'report';

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getReporterId()
    {
        return $this->reporter_id;
    }

    public function setReporterId($reporter_id)
    {
        $this->reporter_id = $reporter_id;

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

    public function getPostId()
    {
        return $this->post_id;
    }

    public function setPostId($post_id)
    {
        $this->post_id = $post_id;

        return $this;
    }

    public function getPageId()
    {
        return $this->page_id;
    }

    public function setPageId($page_id)
    {
        $this->page_id = $page_id;

        return $this;
    }

    public function getCreatedDate()
    {
        return $this->created_date;
    }

    public function setCreatedDate($created_date)
    {
        $this->created_date = $created_date;

        return $this;
    }

    public function getTreatmentDate()
    {
        return $this->treatment_date;
    }

    public function setTreatmentDate($treatment_date)
    {
        $this->treatment_date = $treatment_date;

        return $this;
    }

    public function getTreated()
    {
        return $this->treated;
    }

    public function setTreated($treated)
    {
        $this->treated = $treated;

        return $this;
    }

    public function getReason()
    {
        return $this->reason;
    }

    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getValidate()
    {
        return $this->validate;
    }

    public function setValidate($validate)
    {
        $this->validate = $validate;

        return $this;
    }
}

<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class SubmissionUserCriteria extends AbstractModel
{
    protected $id;
    protected $submission_id;
    protected $user_id;
    protected $criteria_id;
    protected $points;
    protected $overwritten;

    protected $prefix = 'submission_user_criteria';

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getSubmissionId()
    {
        return $this->submission_id;
    }

    public function setSubmissionId($submission_id)
    {
        $this->submission_id = $submission_id;

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

    public function getCriteriaId()
    {
        return $this->criteria_id;
    }

    public function setCriteriaId($criteria_id)
    {
        $this->criteria_id = $criteria_id;

        return $this;
    }

    public function getPoints()
    {
        return $this->points;
    }

    public function setPoints($points)
    {
        $this->points = $points;

        return $this;
    }

    public function getOverwritten()
    {
        return $this->overwritten;
    }

    public function setOverwritten($overwritten)
    {
        $this->overwritten = $overwritten;

        return $this;
    }
}

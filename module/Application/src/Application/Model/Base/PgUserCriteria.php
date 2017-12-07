<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class PgUserCriteria extends AbstractModel
{
    protected $pg_id;
    protected $user_id;
    protected $criteria_id;
    protected $submission_id;
    protected $points;

    protected $prefix = 'pg_user_criteria';

    public function getPgId()
    {
        return $this->pg_id;
    }

    public function setPgId($pg_id)
    {
        $this->pg_id = $pg_id;

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

    public function getSubmissionId()
    {
        return $this->submission_id;
    }

    public function setSubmissionId($submission_id)
    {
        $this->submission_id = $submission_id;

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
}

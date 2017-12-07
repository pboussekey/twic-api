<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class PgUserGrade extends AbstractModel
{
    protected $pg_id;
    protected $user_id;
    protected $submission_id;
    protected $grade;

    protected $prefix = 'pg_user_grade';

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

    public function getSubmissionId()
    {
        return $this->submission_id;
    }

    public function setSubmissionId($submission_id)
    {
        $this->submission_id = $submission_id;

        return $this;
    }

    public function getGrade()
    {
        return $this->grade;
    }

    public function setGrade($grade)
    {
        $this->grade = $grade;

        return $this;
    }
}

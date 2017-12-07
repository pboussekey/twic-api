<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class SubmissionUser extends AbstractModel
{
    protected $submission_id;
    protected $user_id;
    protected $grade;
    protected $submit_date;
    protected $start_date;
    protected $end_date;
    protected $overwritten;

    protected $prefix = 'submission_user';

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

    public function getGrade()
    {
        return $this->grade;
    }

    public function setGrade($grade)
    {
        $this->grade = $grade;

        return $this;
    }

    public function getSubmitDate()
    {
        return $this->submit_date;
    }

    public function setSubmitDate($submit_date)
    {
        $this->submit_date = $submit_date;

        return $this;
    }

    public function getStartDate()
    {
        return $this->start_date;
    }

    public function setStartDate($start_date)
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function getEndDate()
    {
        return $this->end_date;
    }

    public function setEndDate($end_date)
    {
        $this->end_date = $end_date;

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

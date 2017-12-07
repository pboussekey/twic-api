<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class SubQuiz extends AbstractModel
{
    protected $id;
    protected $poll_id;
    protected $start_date;
    protected $end_date;
    protected $user_id;
    protected $submission_id;
    protected $grade;

    protected $prefix = 'sub_quiz';

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getPollId()
    {
        return $this->poll_id;
    }

    public function setPollId($poll_id)
    {
        $this->poll_id = $poll_id;

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

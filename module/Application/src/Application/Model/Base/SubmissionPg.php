<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class SubmissionPg extends AbstractModel
{
    protected $user_id;
    protected $submission_id;
    protected $date;
    protected $has_graded;

    protected $prefix = 'submission_pg';

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

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    public function getHasGraded()
    {
        return $this->has_graded;
    }

    public function setHasGraded($has_graded)
    {
        $this->has_graded = $has_graded;

        return $this;
    }
}

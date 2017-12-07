<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class Faq extends AbstractModel
{
    protected $id;
    protected $ask;
    protected $answer;
    protected $course_id;
    protected $created_date;

    protected $prefix = 'faq';

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getAsk()
    {
        return $this->ask;
    }

    public function setAsk($ask)
    {
        $this->ask = $ask;

        return $this;
    }

    public function getAnswer()
    {
        return $this->answer;
    }

    public function setAnswer($answer)
    {
        $this->answer = $answer;

        return $this;
    }

    public function getCourseId()
    {
        return $this->course_id;
    }

    public function setCourseId($course_id)
    {
        $this->course_id = $course_id;

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
}

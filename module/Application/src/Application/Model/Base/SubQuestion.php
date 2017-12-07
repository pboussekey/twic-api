<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class SubQuestion extends AbstractModel
{
    protected $id;
    protected $sub_quiz_id;
    protected $poll_item_id;
    protected $bank_question_id;
    protected $group_question_id;
    protected $point;
    protected $answered_date;

    protected $prefix = 'sub_question';

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getSubQuizId()
    {
        return $this->sub_quiz_id;
    }

    public function setSubQuizId($sub_quiz_id)
    {
        $this->sub_quiz_id = $sub_quiz_id;

        return $this;
    }

    public function getPollItemId()
    {
        return $this->poll_item_id;
    }

    public function setPollItemId($poll_item_id)
    {
        $this->poll_item_id = $poll_item_id;

        return $this;
    }

    public function getBankQuestionId()
    {
        return $this->bank_question_id;
    }

    public function setBankQuestionId($bank_question_id)
    {
        $this->bank_question_id = $bank_question_id;

        return $this;
    }

    public function getGroupQuestionId()
    {
        return $this->group_question_id;
    }

    public function setGroupQuestionId($group_question_id)
    {
        $this->group_question_id = $group_question_id;

        return $this;
    }

    public function getPoint()
    {
        return $this->point;
    }

    public function setPoint($point)
    {
        $this->point = $point;

        return $this;
    }

    public function getAnsweredDate()
    {
        return $this->answered_date;
    }

    public function setAnsweredDate($answered_date)
    {
        $this->answered_date = $answered_date;

        return $this;
    }
}

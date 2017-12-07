<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class SubAnswer extends AbstractModel
{
    protected $id;
    protected $sub_question_id;
    protected $bank_question_item_id;
    protected $answer;
    protected $date;
    protected $time;

    protected $prefix = 'sub_answer';

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getSubQuestionId()
    {
        return $this->sub_question_id;
    }

    public function setSubQuestionId($sub_question_id)
    {
        $this->sub_question_id = $sub_question_id;

        return $this;
    }

    public function getBankQuestionItemId()
    {
        return $this->bank_question_item_id;
    }

    public function setBankQuestionItemId($bank_question_item_id)
    {
        $this->bank_question_item_id = $bank_question_item_id;

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

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    public function getTime()
    {
        return $this->time;
    }

    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }
}

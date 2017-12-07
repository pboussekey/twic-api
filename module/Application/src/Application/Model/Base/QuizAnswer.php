<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class QuizAnswer extends AbstractModel
{
    protected $id;
    protected $quiz_question_id;
    protected $text;
    protected $is_correct;
    protected $order;

    protected $prefix = 'quiz_answer';

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getQuizQuestionId()
    {
        return $this->quiz_question_id;
    }

    public function setQuizQuestionId($quiz_question_id)
    {
        $this->quiz_question_id = $quiz_question_id;

        return $this;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    public function getIsCorrect()
    {
        return $this->is_correct;
    }

    public function setIsCorrect($is_correct)
    {
        $this->is_correct = $is_correct;

        return $this;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }
}

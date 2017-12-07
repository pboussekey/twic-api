<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class QuizUser extends AbstractModel
{
    protected $id;
    protected $quiz_id;
    protected $quiz_question_id;
    protected $quiz_answer_id;
    protected $user_id;
    protected $text;

    protected $prefix = 'quiz_user';

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getQuizId()
    {
        return $this->quiz_id;
    }

    public function setQuizId($quiz_id)
    {
        $this->quiz_id = $quiz_id;

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

    public function getQuizAnswerId()
    {
        return $this->quiz_answer_id;
    }

    public function setQuizAnswerId($quiz_answer_id)
    {
        $this->quiz_answer_id = $quiz_answer_id;

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

    public function getText()
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }
}

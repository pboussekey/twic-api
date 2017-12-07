<?php

namespace Application\Model;

use Application\Model\Base\Quiz as BaseQuiz;

class Quiz extends BaseQuiz
{
    protected $quiz_question;

    /**
     * Get the value of Quiz Question
     *
     * @return mixed
     */
    public function getQuizQuestion()
    {
        return $this->quiz_question;
    }

    /**
     * Set the value of Quiz Question
     *
     * @param mixed quiz_question
     *
     * @return self
     */
    public function setQuizQuestion($quiz_question)
    {
        $this->quiz_question = $quiz_question;

        return $this;
    }
}

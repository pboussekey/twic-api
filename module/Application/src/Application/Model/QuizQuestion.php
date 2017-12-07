<?php

namespace Application\Model;

use Application\Model\Base\QuizQuestion as BaseQuizQuestion;

class QuizQuestion extends BaseQuizQuestion
{
    const TYPE_SIMPLE = 'simple';
    const TYPE_MULTIPLE = 'multiple';
    const TYPE_TEXT = 'text';

    protected $quiz_answer;

    /**
     * Get the value of Quiz Answer
     *
     * @return mixed
     */
    public function getQuizAnswer()
    {
        return $this->quiz_answer;
    }

    /**
     * Set the value of Quiz Answer
     *
     * @param mixed quiz_answer
     *
     * @return self
     */
    public function setQuizAnswer($quiz_answer)
    {
        $this->quiz_answer = $quiz_answer;

        return $this;
    }
}

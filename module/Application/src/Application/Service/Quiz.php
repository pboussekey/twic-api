<?php

namespace Application\Service;

use Dal\Service\AbstractService;

class Quiz extends AbstractService
{

  /**
   * Create Quiz
   *
   * @invokable
   *
   * @param  string $name
   * @param  string $item_id
   * @param  string $attempt_count
   * @param  string $time_limit
   * @param  array  $questions
   *
   * @return int
   */
    public function add($name = null, $item_id = null, $attempt_count = null, $time_limit = null, $questions = null)
    {
        $identity = $this->getServiceUser()->getIdentity();
        $m_quiz = $this->getModel()
      ->setName($name)
      ->setItemId($item_id)
      ->setUserId($identity['id'])
      ->setAttemptCount($attempt_count)
      ->setTimeLimit($time_limit);

        if (!$this->getMapper()->insert($m_quiz)) {
            throw new \Exception("Error Processing Request", 1);
        }

        $quiz_id = (int) $this->getMapper()->getLastInsertValue();

        if (null !== $questions) {
            $this->getServiceQuizQuestion()->add($questions, $quiz_id);
        }

        return $quiz_id;
    }

    /**
     * Add Quiz Question
     *
     * @invokable
     *
     * @param  array $questions
     * @param  string $id
     */
    public function addQuestions($questions, $id = null)
    {
        return $this->getServiceQuizQuestion()->add($questions, $id);
    }

    /**
     * Add Quiz Answer
     *
     * @invokable
     *
     * @param  array $answers
     * @param  string $quiz_question_id
     */
    public function addAnswers($answers, $quiz_question_id = null)
    {
        return $this->getServiceQuizAnswer()->add($answers, $quiz_question_id);
    }

    /**
     * Update Quiz Question
     *
     * @invokable
     *
     * @param array $questions
     */
    public function updateQuestions($questions)
    {
        return $this->getServiceQuizQuestion()->update($questions);
    }

    /**
     * Update Quiz Answer
     *
     * @invokable
     *
     * @param array $answers
     */
    public function updateAnswers($answers)
    {
        return $this->getServiceQuizAnswer()->update($answers);
    }

    /**
     * Remove Quiz Question
     *
     * @invokable
     *
     * @param  string $quiz_question_id
     */
    public function removeQuestions($quiz_question_id)
    {
        return $this->getServiceQuizQuestion()->remove($quiz_question_id);
    }

    /**
     * Remove Quiz Answer
     *
     * @invokable
     *
     * @param  int $quiz_answer_id
     */
    public function removeAnswers($quiz_answer_id)
    {
        return $this->getServiceQuizAnswer()->remove($quiz_answer_id);
    }

    /**
     * Get User Answer
     *
     * @invokable
     *
     * @param int $id
     * @param int $user_id
     */
    public function getUserAnswer($id, $user_id = null)
    {
        return $this->getServiceQuizUser()->get($id, $user_id);
    }

    /**
     * Add User Answer
     *
     * @invokable
     *
     * @param  int $quiz_question_id
     * @param  int $quiz_answer_id
     * @param  string $text
     */
    public function addUserAnswer($quiz_question_id, $quiz_answer_id = null, $text = null)
    {
        return $this->getServiceQuizUser()->add($quiz_question_id, $quiz_answer_id, $text);
    }

    /**
     * Update User Answer
     *
     * @invokable
     *
     * @param  int $id
     * @param  int $quiz_answer_id
     * @param  string $text
     */
    public function updateUserAnswer($id, $quiz_answer_id = null, $text = null)
    {
        return $this->getServiceQuizUser()->update($id, $quiz_answer_id, $text);
    }

    /**
     * Remove User Answer
     *
     * @invokable
     *
     * @param  int $id
     */
    public function removeUserAnswer($id)
    {
        return $this->getServiceQuizUser()->remove($id);
    }

    /**
     * Get Quiz
     *
     * @invokable
     *
     * @param  string $id
     *
     * @return int
     */
    public function get($id)
    {
        if (!is_array($id)) {
            $id = [$id];
        }

        $ret = [];
        foreach ($id as $i) {
            $m_quiz = $this->getLite($id);
            $m_quiz->setQuizQuestion($this->getServiceQuizQuestion()->get($i));

            $ret[$i] = $m_quiz->toArray();
        }

        return $ret;
    }

    /**
    * Get Lite Model Quiz
    * @param int $id
    *
    * @return \Application\Model\Quiz
    **/
    public function getLite($id)
    {
        return $this->getMapper()->select($this->getModel()->setId($id))->current();
    }

    public function update($id, $item_id = null, $name = null, $attempt_count = null, $time_limit = null)
    {
        $m_quiz = $this->getModel()
      ->setId($id)
      ->setItemId($item_id)
      ->setName($name)
      ->setAttemptCount($attempt_count)
      ->setTimeLimit($time_limit);

        return $this->getMapper()->update($m_quiz);
    }

    /**
     * Get Service SQuiz Question
     *
     * @return \Application\Service\QuizQuestion
     */
    public function getServiceQuizQuestion()
    {
        return $this->container->get('app_service_quiz_question');
    }

    /**
     * Get Service User
     *
     * @return \Application\Service\User
     */
    public function getServiceUser()
    {
        return $this->container->get('app_service_user');
    }

    /**
     * Get Service Quiz Answer
     *
     * @return \Application\Service\QuizAnswer
     */
    public function getServiceQuizAnswer()
    {
        return $this->container->get('app_service_quiz_answer');
    }

    /**
     * Get Service Quiz User
     *
     * @return \Application\Service\QuizUser
     */
    public function getServiceQuizUser()
    {
        return $this->container->get('app_service_quiz_user');
    }
}

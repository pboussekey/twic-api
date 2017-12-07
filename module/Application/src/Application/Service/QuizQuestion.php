<?php

namespace Application\Service;

use Dal\Service\AbstractService;
use Application\Model\QuizQuestion as ModelQuizQuestion;

class QuizQuestion extends AbstractService
{
    /**
     * Add Quiz Question
     *
     * @param  string $quiz_id
     * @param  array $questions
     *
     * @return int
     */
    public function add($questions, $quiz_id = null)
    {
        $ret = [];
        foreach ($questions as $question) {
            if (isset($question['quiz_id'])) {
                $quiz_id = $question['quiz_id'];
            }
            if (null !== $quiz_id) {
                $text    = (isset($question['text'])) ? $question['text'] : null;
                $type    = $question['type'];
                $point   = (isset($question['point'])) ? $question['point'] : null;
                $order   = (isset($question['order'])) ? $question['order'] : null;
                $answers = (isset($question['answers'])) ? $question['answers'] : null;
                $ret[] = $this->_add($quiz_id, $text, $type, $point, $order, $answers);
            }
        }

        return $ret;
    }

    public function getLite($id)
    {
        return $this->getMapper()->select($this->getModel()->setId($id));
    }

    public function remove($id)
    {
        return $this->getMapper()->delete($this->getModel()->setId($id));
    }

    /**
     * Update Quiz Question

     * @param array $questions
     */
    public function update($questions)
    {
        $ret = [];
        foreach ($questions as $question) {
            $id      = $question['id'];
            $text    = (isset($question['text'])) ? $question['text'] : null;
            $type    = (isset($question['type'])) ? $question['type'] : null;
            $point   = (isset($question['point'])) ? $question['point'] : null;

            $ret[] = $this->getMapper()->update($this->getModel()->setText($text)->setType($type)->setPoint($point)->setId($id));
        }

        return $ret;
    }

    public function get($quiz_id, $has_answer = null)
    {
        $identity = $this->getServiceUser()->getIdentity();
        $res_quiz_question = $this->getMapper()->select($this->getModel()->setQuizId($quiz_id));

        $m_quiz = $this->getServiceQuiz()->getLite($quiz_id);
        if ($m_quiz && is_numeric($m_quiz->getItemId())) {
            if (null === $has_answer) {
                $m_item = $this->getServiceItem()->getLite($m_quiz->getItemId())->current();
                $page_id = $m_item->getPageId();
                $ar_pu = $this->getServicePageUser()->getListByPage($page_id, 'admin');
                $has_answer = ($m_item->getIsGradePublished() == true || in_array($identity['id'], $ar_pu[$page_id]));
            }
            foreach ($res_quiz_question as $m_quiz_question) {
                if (!(!$has_answer && $m_quiz_question->getType() === ModelQuizQuestion::TYPE_TEXT)) {
                    $m_quiz_question->setQuizAnswer($this->getServiceQuizAnswer()->get($m_quiz_question->getId(), $has_answer));
                }
            }
        } else {
            foreach ($res_quiz_question as $m_quiz_question) {
                $m_quiz_question->setQuizAnswer($this->getServiceQuizAnswer()->get($m_quiz_question->getId()));
            }
        }

        return $res_quiz_question;
    }

    public function _add($quiz_id, $text, $type, $point = null, $order = null, $answers = null)
    {
        $m_quiz_question = $this->getModel()->setText($text)->setType($type)->setPoint($point)->setQuizId($quiz_id);
        if ($this->getMapper()->insert($m_quiz_question) <= 0) {
            throw new \Exception("Error Processing Request", 1);
        }

        $quiz_question_id = (int) $this->getMapper()->getLastInsertValue();

        if (null !== $answers) {
            $this->getServiceQuizAnswer()->add($answers, $quiz_question_id);
        }

        return $quiz_question_id;
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
     * Get Service Quiz
     *
     * @return \Application\Service\Quiz
     */
    public function getServiceQuiz()
    {
        return $this->container->get('app_service_quiz');
    }

    /**
     * Get Service Item
     *
     * @return \Application\Service\Item
     */
    public function getServiceItem()
    {
        return $this->container->get('app_service_item');
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
     * Get Service Page User
     *
     * @return \Application\Service\PageUser
     */
    public function getServicePageUser()
    {
        return $this->container->get('app_service_page_user');
    }
}

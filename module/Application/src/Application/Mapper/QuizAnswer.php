<?php

namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;

class QuizAnswer extends AbstractMapper
{
    public function get($quiz_question_id, $has_answer)
    {
        $colunm = ($has_answer) ?
    ['id', 'quiz_question_id', 'text', 'is_correct', 'order'] :
    ['id', 'quiz_question_id', 'text', 'order'];
        $select = $this->tableGateway->getSql()->select();
        $select->columns($colunm);
        $select->where(['quiz_question_id' => $quiz_question_id]);

        return $this->selectWith($select);
    }
}

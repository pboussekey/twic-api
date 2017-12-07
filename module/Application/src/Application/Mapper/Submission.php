<?php

namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;
use Zend\Db\Sql\Expression;

class Submission extends AbstractMapper
{
    public function get($id = null, $item_id = null, $user_id = null, $group_id = null)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['id', 'item_id',
      'submission$submit_date' => new Expression('DATE_FORMAT(submission.submit_date, "%Y-%m-%dT%TZ")'),
      'is_graded', 'post_id']);
      
        if ($user_id !== null || $group_id !== null) {
            $select->join('item_user', 'item_user.submission_id=submission.id', []);
        }
        if ($user_id !== null) {
            $select->where(['item_user.user_id' => $user_id]);
        }
        if ($group_id !== null) {
            $select->where(['item_user.group_id' => $group_id]);
        }
      
        if ($item_id !== null) {
            $select->where(['submission.item_id' => $item_id]);
        }
      
        if ($id !== null) {
            $select->where(['submission.id' => $id]);
        }
        $select->quantifier('DISTINCT');


        return $this->selectWith($select);
    }
}

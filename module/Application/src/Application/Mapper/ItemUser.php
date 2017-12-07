<?php
namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;
use Zend\Db\Sql\Expression;

class ItemUser extends AbstractMapper
{
    public function getList($item_id, $user_id = null, $submission_id = null)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns([
            'id',
            'user_id',
            'item_id',
            'submission_id',
            'rate'
        ])
            ->join('group', 'item_user.group_id=group.id', [
            'group!id' => 'id',
            'name'
        ], $select::JOIN_LEFT)
            ->join('submission', 'item_user.submission_id=submission.id', [
            'id',
            'is_graded',
            'submission$submit_date' => new Expression('DATE_FORMAT(submission.submit_date, "%Y-%m-%dT%TZ")'),
            'post_id'
        ], $select::JOIN_LEFT)
            ->where([
            'item_user.deleted_date IS NULL'
        ])
            ->where([
            'item_user.item_id' => $item_id
        ]);
        if (null !== $user_id) {
            $select->where([
                'item_user.user_id' => $user_id
            ]);
        }
        if (null !== $submission_id) {
            $select->where([
                'item_user.submission_id' => $submission_id
            ]);
        }
        
        return $this->selectWith($select);
    }

    public function getListUserId($group_id = null, $item_id = null)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['user_id']);
        
        if(null !== $group_id) {
            $select->where(['item_user.group_id' => $group_id]);
        }
        if(null !== $item_id) {
            //TODO CHECK IF MEMBER
            $select->where(['item_user.item_id' => $item_id]);
        }
        
        return $this->selectWith($select);
    }
}

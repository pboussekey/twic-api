<?php

namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;
use Zend\Db\Sql\Predicate\Expression;
use Application\Model\Page as ModelPage;

class Message extends AbstractMapper
{
    public function getList($user_id, $conversation_id = null, $message_id = null)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['id', 'text', 'user_id','message$created_date' => new Expression('DATE_FORMAT(message.created_date, "%Y-%m-%dT%TZ")')])
            ->join(['message_library' => 'library'], 'message_library.id=message.library_id', ['message_library!id' => 'id', 'name', 'link', 'token', 'type', 'box_id'], $select::JOIN_LEFT)
            ->join(['message_message_user' => 'message_user'], new Expression('message.id=message_message_user.message_id AND message_message_user.user_id = ?', [$user_id]), ['message_message_user$read_date' => new Expression('DATE_FORMAT(message_message_user.read_date, "%Y-%m-%dT%TZ")')], $select::JOIN_LEFT)
            ->join('user', 'message_message_user.from_id = user.id', [])
            ->where(['message_message_user.deleted_date IS NULL AND user.deleted_date IS NULL'])
            ->order(['message.id DESC']);

        if (null !== $message_id) {
            $select->where(['message.id' => $message_id]);
        }
        if (null !== $conversation_id) {
            $select->where(['message.conversation_id' => $conversation_id]);
        }

        return $this->selectWith($select);
    }
    
    
    
    public function getCount($me, $interval, $start_date = null, $end_date = null, $page_id = null, $type = null, $date_offset = 0)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns([ 'message$created_date' => new Expression('SUBSTRING(DATE_SUB(message.created_date, INTERVAL '.$date_offset. ' HOUR ),1,'.$interval.')'), 'message$count' => new Expression('COUNT(DISTINCT message.id)')])
            ->join('conversation', 'message.conversation_id = conversation.id', ['message$type' => 'type'])
            ->group([new Expression('SUBSTRING(DATE_SUB(message.created_date, INTERVAL '.$date_offset. ' HOUR ),1,'.$interval.')'), 'conversation.type']);

        if (null != $start_date) {
            $select->where(['message.created_date >= ? ' => $start_date]);
        }

        if (null != $end_date) {
            $select->where(['message.created_date <= ? ' => $end_date]);
        }

        if (null != $type) {
            $select->where(['conversation.type' => $type]);
        }
        
        if (null != $page_id) {
            $select->join('user', 'message.user_id = user.id', [])
                ->join('page', 'page.conversation_id = conversation.id',[], $select::JOIN_LEFT)
                ->where->NEST->NEST
                ->in('page.id',$page_id)
                ->notEqualTo(' page.type',ModelPage::TYPE_ORGANIZATION )->UNNEST->OR
                ->in(' user.organization_id', $page_id)->UNNEST;
        }
        
        return $this->selectWith($select);
    }
}

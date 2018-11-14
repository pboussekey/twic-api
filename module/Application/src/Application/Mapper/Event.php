<?php

namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;
use Zend\Db\Sql\Predicate\Expression;

class Event extends AbstractMapper
{
    public function getList($user_id, $events = null, $unread = null, $start_date = null)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns([
            'id',
            'user_id',
            'source',
            'event$date' => new Expression('DATE_FORMAT(event.date, "%Y-%m-%dT%TZ")'),
            'event',
            'object',
            'target'])
                ->join('event_user',
                  new Expression('event_user.event_id = event.id AND event_user.user_id = '.$user_id),
                  ['event$read_date' => 'read_date', 'event$text' => 'text', 'event$picture' => 'picture']
                )
                ->where(['event.user_id != ? ' => $user_id]);
        if(null !== $events){
            $select->where(['event.event' => $events]);
        }
        if($unread === true){
            $select->where('event_user.read_date IS NULL');
        }
        if (null != $start_date) {
            $select->where(['date >= ? ' => $start_date]);
        }
        return $this->selectWith($select);
    }
}

<?php

namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Select;

class Event extends AbstractMapper
{
    public function getList($user_id, $events = null, $unread = null)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns([
            'id',
            'user_id',
            'source',
            'event$date' => new Expression('DATE_FORMAT(event.date, "%Y-%m-%dT%TZ")'),
            'event',
            'object',
            'picture',
            'target_id',
            'event$text' => new Expression(
              'replace(event.text, "{user}", CASE event.target_id WHEN event_user.user_id THEN "your" WHEN event.user_id THEN "their" ELSE CONCAT("<b>", user.firstname, " ", user.lastname,"</b>\'s") END)'
            ),
            'target'])
                ->join('event_user',
                  new Expression('event_user.event_id = event.id AND event_user.user_id = '.$user_id),
                  ['event$read_date' => 'read_date']
                )
                -> join('user', 'event.target_id = user.id', [], $select::JOIN_LEFT)
                ->where(['event.user_id != ? ' => $user_id]);
        if(null !== $events){
            $select->where(['event.event' => $events]);
        }
        if($unread === true){
            $select->where('event_user.read_date IS NULL');
        }
        return $this->selectWith($select);
    }

    public function getListUnseen($user_id, $events = null, $inactive_since = 7)
    {

        $max_select = $this->tableGateway->getSql()->select();
        $max_select->columns([
          'event',
          'id' => new Expression('MAX(event.id)')
        ])
        ->join('event_user', 'event.id = event_user.event_id', ['user_id' => 'user_id'])
        ->where(['event_user.user_id' => $user_id])
        ->group(['event.event','event_user.user_id']);


        $activity_select = new Select('activity');
        $activity_select->columns([
          'date' => new Expression('MAX(date)'),
          'user_id' => 'user_id'
        ])
        ->where(['activity.user_id' => $user_id])
        ->group(['activity.user_id']);


        $count_select = $this->tableGateway->getSql()->select();
        $count_select->columns([
            'event' => 'event',
            'count' => new Expression('COUNT(DISTINCT event.id)')
        ])
        ->join('event_user', 'event.id = event_user.event_id', ['user_id' => 'user_id'])
        ->where(['event_user.user_id' => $user_id])
        ->group(['event.event','event_user.user_id']);

          $select = $this->tableGateway->getSql()->select();
          $select->columns([
              'event$date' => new Expression('DATE_FORMAT(MAX(event.date), "%M %D")'),
              'event',
              'picture',
              'object',
              'event$text' => new Expression(
              'COALESCE(REPLACE(event.text,
                    "{user}",
                    CASE event.target_id
                        WHEN event_user.user_id THEN "your"
                        WHEN event.user_id THEN "their"
                        ELSE CONCAT("<b>", user.firstname, " ", user.lastname,"</b>\'s") END),
                    event.text)'
              ),
              'target'])
                  ->join('event_user',  new Expression('event_user.event_id = event.id'),  ['event$user_id' => 'user_id'])
                  ->join(['max' => $max_select], 'max.id = event.id AND max.user_id = event_user.user_id', [])
                  ->join('user',  new Expression('user.id =  event.target_id AND user.is_active IS TRUE'),  [], $select::JOIN_LEFT)
                  ->join(['count' => $count_select], 'count.user_id = event_user.user_id AND count.event = event.event', ['event$count' => 'count'])
                  ->join(['activity' => $activity_select], 'activity.user_id = event_user.user_id AND event.date >= activity.date', [])
                  ->where(['activity.date <=  DATE_SUB(NOW(), INTERVAL ? DAY)' => $inactive_since])
                  ->where(['event.text IS NOT NULL'])
                  ->where(['event_user.user_id' => $user_id])
                  ->where('event.user_id <> event_user.user_id')
                  ->group(['event_user.user_id', 'event.event'])
                  ->order(['event_user.user_id', 'event.id DESC']);
          if(null !== $events){
              $select->where(['event.event' => $events]);
          }
          syslog(1, $this->printSql($select));
          return $this->selectWith($select);
    }
}

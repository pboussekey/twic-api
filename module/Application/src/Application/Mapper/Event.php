<?php

namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Select;

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
            'picture',
            'event$text' => new Expression(
              'replace(event.text, "{user}", CASE event.target_id WHEN '.$user_id.' THEN "your" ELSE CONCAT("<b>", user.firstname, " ", user.lastname,"</b>\'s") END)'
            ),
            'target'])
                ->join('event_user',
                  new Expression('event_user.event_id = event.id AND event_user.user_id = '.$user_id),
                  ['event$read_date' => 'read_date']
                )
                -> join('user', 'event.user_id = user.id', [])
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

    public function getListUnseen($user_id, $events = null)
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
            'event$text' => new Expression(
              'replace(event.text, "{user}", CASE event.target_id WHEN event_user.user_id THEN "your" WHEN event.user_id THEN "their" ELSE CONCAT("<b>", user.firstname, " ", user.lastname,"</b>\'s") END)'
            ),
            'target'])
                ->join('event_user',  new Expression('event_user.event_id = event.id'),  ['event$user_id' => 'user_id'])
                ->join(['max' => $max_select], 'max.id = event.id AND max.user_id = event_user.user_id', [])
                ->join('user',  'user.id =  event.target_id',  [])
                ->join(['count' => $count_select], 'count.user_id = event_user.user_id AND count.event = event.event', ['event$count' => 'count'])
                ->join(['activity' => $activity_select], 'activity.user_id = event_user.user_id AND event.date >= activity.date ', [], $select::JOIN_LEFT)
                ->where(['event.text IS NOT NULL'])
                ->where(['event_user.user_id' => $user_id])
                ->where('event.user_id <> event_user.user_id')
                ->group(['user.id', 'event.event'])
                ->order(['event_user.user_id', 'event.id DESC']);
        if(null !== $events){
            $select->where(['event.event' => $events]);
        }
        return $this->selectWith($select);
    }
}

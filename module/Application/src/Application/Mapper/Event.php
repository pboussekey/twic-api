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

    public function getListUnseen($users, $events = null)
    {


      $events_select  = $this->tableGateway->getSql()->select();
      $events_select->columns([
        'event',
        'id' => new Expression('MAX(event.id)'),
        'important' => new Expression('MAX(event.important)'),
        'count' =>  new Expression('COUNT(DISTINCT event.id)')
      ])
      ->join('event_user', new Expression('event.id = event_user.event_id AND event.text IS NOT NULL AND event.date >=  DATE_SUB(NOW(), INTERVAL 7 DAY) '), ['user_id' => 'user_id'])
      ->join('user', 'event_user.user_id = user.id', [])
      ->where('user.is_active = 1')
      ->where(['event_user.user_id' => $users])
      ->where(new Expression(' NOT EXISTS (SELECT id FROM activity WHERE date > event.date AND activity.user_id = event_user.user_id)'))
      ->group(['event', ' event_user.user_id']);



      $select  = $this->tableGateway->getSql()->select();
      $select->columns([
          'id',
          'source',
          'event$date' => new Expression('DATE_FORMAT(event.date, "%M %D")'),
          'event',
          'object',
          'target',
          'event$text' => new Expression("
              COALESCE(REPLACE(event.text,
                  '{user}',
                  CASE event.target_id
                      WHEN events.user_id THEN 'your'
                      WHEN event.user_id THEN 'their'
                      ELSE CONCAT('<b>',
                              target.firstname,
                              ' ',
                              target.lastname,
                              '</b>\'s')
                  END), event.text)
            "),
            'picture',
            'target_id'
      ])
      ->join(['events' => $events_select], 'event.id = events.id', ['event$user_id' => 'user_id', 'event$count' => 'count', 'event$important' => 'important'])
      ->join(['target' => 'user'], 'event.target_id = target.id', [], $select::JOIN_LEFT)
      ->order(['events.user_id DESC', 'events.important DESC', 'event.id DESC']);


       return $this->selectWith($select);
    }
}

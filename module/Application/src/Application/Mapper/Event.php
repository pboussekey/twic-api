<?php

namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Select;

class Event extends AbstractMapper
{
    public function getList($user_id, $events = null, $unread = null)
    {


        $events_select  = $this->tableGateway->getSql()->select();
        $events_select->columns([
          'id' => new Expression('MAX(event.id)'),
          'count' => new Expression('COUNT(DISTINCT event.user_id)')
        ])
        ->join('event_user', 'event.id = event_user.event_id' ,[])
        ->join('user', 'event_user.user_id = user.id', [])
        ->where(['event.user_id <> ? ' => $user_id])
        ->where(['event_user.user_id' => $user_id])
        ->group(new Expression('COALESCE(event.uid, event.id)'));

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
              ' replace(
                    REPLACE(event.text, "{user}",
                      CASE
                        WHEN event_user.user_id  = event.target_id THEN "your"
                        WHEN event.user_id = event.target_id AND previous_user.id IS NULL THEN "their"
                        ELSE CONCAT("<b>", COALESCE(user.firstname,""), " ", COALESCE(user.lastname,""),"</b>\'s") END),
                    "{more}",
                    CASE WHEN events.count = 2 AND previous_user.id IS NOT NULL THEN CONCAT(" and <b>", previous_user.firstname, " ", previous_user.lastname,"</b>")
                         WHEN events.count > 2 THEN CONCAT(" and ", events.count - 1, " others")
                         ELSE "" END)'
            ),
            'target'])
                ->join('event_user',
                  new Expression('event_user.event_id = event.id AND event_user.user_id = '.$user_id),
                  ['event$read_date' => 'read_date']
                )
                ->join('user', 'event.target_id = user.id', [], $select::JOIN_LEFT)
                ->join(['previous' => 'event'], new Expression('event.previous_id = previous.id AND previous.user_id <> ? ', $user_id), [], $select::JOIN_LEFT)
                ->join(['previous_user' => 'user'], 'previous.user_id = previous_user.id', [], $select::JOIN_LEFT)
                ->join(['events' => $events_select], 'events.id = event.id', [])
                ->where(['event.user_id <> ? ' => $user_id]);
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
          'academic' => new Expression('MAX(event.academic)'),
          'count' =>  new Expression('COUNT(DISTINCT event.user_id)')
        ])
        ->join('event_user', new Expression('event.id = event_user.event_id AND event.text IS NOT NULL AND event.date >=  DATE_SUB(NOW(), INTERVAL 7 DAY) '), ['user_id' => 'user_id'])
        ->join('user', 'event_user.user_id = user.id', [])
        ->join('activity', new Expression('event_user.user_id = activity.user_id AND activity.date >= event.date'), [], $events_select::JOIN_LEFT)
        ->where('user.is_active = 1')
        ->where('(( event.academic IS TRUE AND user.has_academic_notifier) OR (event.academic IS FALSE AND user.has_social_notifier))')
        ->where('activity.id IS NULL')
        ->where(['event_user.user_id' => $users])
        ->group(['event.uid', 'event.event', ' event_user.user_id']);



        $select  = $this->tableGateway->getSql()->select();
        $select->columns([
            'id',
            'source',
            'event$date' => new Expression('DATE_FORMAT(event.date, "%M %D")'),
            'event',
            'object',
            'target',
            'event$text' => new Expression("
                REPLACE(REPLACE(event.text,
                    '{user}',
                    CASE event.target_id
                        WHEN events.user_id THEN 'your'
                        WHEN event.user_id THEN 'their'
                        ELSE CONCAT('<b>',
                                COALESCE(target.firstname,''),
                                ' ',
                                COALESCE(target.lastname,''),
                                '</b>\'s')
                    END), '{more}',
                    CASE
                        WHEN events.count = 2 AND previous_user.id IS NOT NULL THEN CONCAT('and <b>', previous_user.firstname, ' ', previous_user.lastname, '</b>')
                        WHEN events.count > 2 THEN CONCAT(' and ', events.count - 1,' others')  ELSE '' END)
              "),
              'picture',
              'target_id'
        ])
        ->join(['events' => $events_select], 'event.id = events.id', ['event$user_id' => 'user_id', 'event$count' => 'count', 'event$academic' => 'academic'])
        ->join(['target' => 'user'], 'event.target_id = target.id', [], $select::JOIN_LEFT)
        ->join(['previous' => 'event'],  new Expression('event.previous_id = previous.id AND previous.user_id <> events.user_id'), [], $select::JOIN_LEFT)
        ->join(['previous_user' => 'user'], 'previous.user_id = previous_user.id', [], $select::JOIN_LEFT)
        ->order(['events.user_id DESC', 'events.academic DESC', 'event.id DESC']);

        return $this->selectWith($select);
    }

    public function getLast($uid, $user_id)
    {
      $select  = $this->tableGateway->getSql()->select();
      $select->columns([
          'event$id' => new Expression('MAX(id)'),
          'event$count' => new Expression('COUNT(DISTINCT event.user_id)')
      ])
      ->where(['uid' => $uid])
      ->where(['user_id <> ? ' => $user_id]);

      return $this->selectWith($select);
    }
}

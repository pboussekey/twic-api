<?php

namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Predicate\Predicate;
use Application\Model\Role as ModelRole;

class Activity extends AbstractMapper
{
    public function aggregate($event, $user, $object_id = null, $object_name = null, $target_id = null, $target_name = null)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['event', 'activity$value_user' => new Expression('SUM( IF(activity.user_id='.$user.', object_value, 0))'), 'activity$value_total' => new Expression('SUM(object_value)')])->where(['event' => $event]);
        if (null !== $object_name && null !== $object_id) {
            $select->where(['object_id' => $object_id, 'object_name' => $object_name]);
        }
        if (null !== $target_name && null !== $target_id) {
            $select->where(['target_id' => $target_id, 'target_name' => $target_name]);
        }

        $select->join('user_role', 'user_role.user_id=activity.user_id')
            ->where(['user_role.role_id='.ModelRole::ROLE_USER_ID.'']);

        return $this->selectWith($select);
    }
    
    public function getConnectionCount($me, $interval, $start_date = null, $end_date = null, $organization_id = null)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns([ 'activity$date' => new Expression('SUBSTRING(date,1,'.$interval.')'), 'activity$count' => new Expression('COUNT(activity.id)')])
            ->join('user', 'activity.user_id = user.id', ['activity$linkedin_id' => new Expression('IF(linkedin_id IS NOT NULL, 1, 0)')])
            ->group([new Expression('SUBSTRING(date,1,'.$interval.')'), 'user.linkedin_id']);

        if (null != $start_date) {
            $select->where(['date >= ? ' => $start_date]);
        }

        if (null != $end_date) {
            $select->where(['date <= ? ' => $end_date]);
        }

        if (null != $organization_id) {
            $select->where(['organization_id' => $organization_id]);
        }
        
        return $this->selectWith($select);
    }

    public function getListWithFilters($me, $event = null, $object_id = null, $object_name = null, $school_id = null, $program_id = null, 
        $course_id = null, $item_id = null, $user_id = null, $is_academic = false
    ) {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['id', 'event', 'object_name', 'object_data', 'object_value', 'target_name', 'target_data', 'user_id', 'activity$date' => new Expression('DATE_FORMAT(activity.date, "%Y-%m-%dT%TZ")')])
            ->join('sub_conversation', 'sub_conversation.conversation_id = activity.object_id', [])
            ->join('submission', 'sub_conversation.submission_id = submission.id', [])
            ->join('item', 'submission.item_id = item.id', ['activity$item_id' => 'id'])
            ->join('course', 'item.course_id = course.id', ['activity$course_id' => 'id'])
            ->join('program', 'course.program_id = program.id', ['activity$program_id' => 'id','activity$school_id' => 'school_id'])
            ->quantifier('DISTINCT');
        if (null !== $event) {
            $select->where(['event' => $event]);
        }
        if (null !== $object_id) {
            $select->where(['object_id' => $object_id]);
        }
        if (null !== $object_name) {
            $select->where(['object_name' => $object_name]);
        }
        if (null !== $school_id) {
            $select->where(['program.school_id' => $school_id]);
        }
        if (null !== $program_id) {
            $select->where(['program.id' => $program_id]);
        }
        if (null !== $course_id) {
            $select->where(['course.id' => $course_id]);
        }
        if (null !== $item_id) {
            $select->where(['item.id' => $item_id]);
        }
        if (null !== $user_id && !empty($user_id)) {
            $select->where(['activity.user_id' => $user_id]);
        }
        if (!$is_academic) {
            $select->join('course_user_relation', 'course.id = course_user_relation.course_id')
                ->where(['course_user_relation.user_id' => $me ]);
        }

        return $this->selectWith($select);
    }
    

    public function getListWithUser($search)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['id', 'event', 'object_name', 'object_data', 'target_name', 'target_data', 'activity$date' => new Expression('DATE_FORMAT(activity.date, "%Y-%m-%dT%TZ")')])->join('user', 'user.id = activity.user_id', ['firstname',  'lastname', 'nickname', 'avatar']);

        if (null !== $search) {
            $select->where(['CONCAT_WS(" ", user.firstname, user.lastname) LIKE ? ' => ''.$search.'%'], Predicate::OP_OR)
                ->where(['CONCAT_WS(" ", user.lastname, user.firstname) LIKE ? ' => ''.$search.'%'], Predicate::OP_OR)
                ->where(['nickname LIKE ? ' => ''.$search.'%'], Predicate::OP_OR);
        }
        $select->order(['activity.id' => 'DESC']);

        return $this->selectWith($select);
    }

    public function get($id)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['id', 'event', 'object_name', 'object_data', 'target_name', 'target_data']);
        $select->where(['activity.id' => $id]);
      
        return $this->selectWith($select);
    }

    public function getList($search, $start_date, $end_date, $organization_id = null, $user_id = null)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['id', 'user_id', 'event', 'object_name', 'object_data', 'activity$date' => new Expression('DATE_FORMAT(activity.date, "%Y-%m-%dT%TZ")')])
            ->join('user', 'user.id = activity.user_id', ['firstname',  'lastname', 'nickname', 'avatar', 'organization_id']);
        $array = explode(" ", $search);
        if (null != $array) {
            foreach ($array as $value)
            {
                $select->where(['(event LIKE ? ' => '%'.$value.'%'], Predicate::OP_OR)
                    ->where(['organization_id LIKE ? ' => '%'.$value.'%'], Predicate::OP_OR)
                    ->where(['object_name LIKE ? ' => '%'.$value.'%'], Predicate::OP_OR)
                    ->where(['event LIKE ? ' => '%'.$value.'%'], Predicate::OP_OR)
                    ->where(['firstname LIKE ? ' => '%'.$value.'%'], Predicate::OP_OR)
                    ->where(['lastname LIKE ? ) ' => '%'.$value.'%'], Predicate::OP_OR);
            }
        }

        if (null != $start_date) {
            $select->where(['date >= ? ' => $start_date]);
        }

        if (null != $end_date) {
            $select->where(['date <= ? ' => $end_date]);
        }

        if (null != $organization_id) {
            $select->join('page_user', 'user.id = page_user.user_id', [])
                ->where(['page_user.page_id' => $organization_id])
                ->group('activity.id');
        }

        if (null != $user_id) {
            $select->where(['user_id' => $user_id]);
        } 

        return $this->selectWith($select);
    }

    public function getPages($object_name,  $start_date, $end_date)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(
            ['object_data', 'date', 
            'activity$object_name' => new Expression(
                'IF(object_name LIKE \'lms.page%\', REPLACE(object_name,\'lms.page\', SUBSTRING_INDEX(SUBSTRING_INDEX(object_data, \'"type":"\', \'-1\'), \'"\', 1)),
                                      REPLACE(object_name, \'lms.page\', \'lms.page\'))'
            ),
            'activity$count'       => new Expression('Count(*)'),
            'activity$min_date'    => new Expression('MIN(date)'),
            'activity$max_date'    => new Expression('MAX(date)')]
        )
            ->group(
                new Expression(
                    'IF(object_name LIKE \'lms.page%\', REPLACE(object_name,\'lms.page\', SUBSTRING_INDEX(SUBSTRING_INDEX(object_data, \'"type":"\', \'-1\'), \'"\', 1)),
                                      REPLACE(object_name, \'lms.page\', \'lms.page\'))'
                )
            )
            ->where(['object_name is not NULL']);

        if (null != $object_name) {
            $select->where(['object_name' => $object_name]);
        }

        if (null != $start_date) {
            $select->where(['date >= ? ' => $start_date]);
        }

        if (null != $end_date) {
            $select->where(['date <= ? ' => $end_date]);
        }

        $select->order(['activity$count' => 'DESC']);
        return $this->selectWith($select);
    }
}
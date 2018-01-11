<?php

namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Predicate\Predicate;
use Application\Model\Page as ModelPage;

class Activity extends AbstractMapper
{
  


    public function getList($search, $start_date, $end_date, $page_id = null, $user_id = null)
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

        if (null != $page_id) {
            $select->join('page_user', 'user.id = page_user.user_id', [], $select::JOIN_LEFT)
                ->join('page', 'page.id = page_user.page_id',[])
                ->group('activity.id')
                ->where->NEST->NEST
                ->in('page_user.page_id',$page_id)
                ->notEqualTo(' page.type',ModelPage::TYPE_ORGANIZATION )->UNNEST->OR
                ->in(' user.organization_id', $page_id)->UNNEST;
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
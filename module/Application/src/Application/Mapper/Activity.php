<?php

namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Predicate\Predicate;
use Zend\Db\Sql\Select;
use Application\Model\Page as ModelPage;
use Application\Model\Role as ModelRole;
use Application\Model\PageUser as ModelPageUser;

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
    
     public function getVisitsPrc($page_id, $start_date, $end_date)
    {
        $select = new Select('page_user');
        $select->columns([ 
            'activity$prc' => new Expression('100 * COUNT(DISTINCT activity.user_id) / COUNT(DISTINCT page_user.user_id)')])
            ->join('activity', new Expression('page_user.user_id = activity.user_id AND 
                    object_name is not NULL AND object_name LIKE \'lms.page%\' AND
                    event = \'navigation\' AND SUBSTRING_INDEX(SUBSTRING_INDEX(object_data, \'"id":"\', \'-1\'), \'"\', 1) IN
                    ('.join(",", $page_id).') AND date BETWEEN ? AND ?',
            [$start_date, $end_date]),[], $select::JOIN_LEFT)
            ->where(['page_user.state = ?' => ModelPageUser::STATE_MEMBER])
            ->where->in('page_user.page_id', $page_id);
        
        return $this->selectWith($select);
    }
    
    public function getVisitsCount($me, $interval, $start_date = null, $end_date = null, $page_id = null)
    {
        $select = $this->tableGateway->getSql()->select();
     
            $select->columns([ 
                'activity$date' => new Expression('SUBSTRING(activity.date,1,'.$interval.')'), 
                'activity$count' => new Expression('COUNT(DISTINCT SUBSTRING(activity.date,1,10), activity.user_id)')]
             )
            ->join('user', 'activity.user_id = user.id', [])
            ->join('user_role', 'user_role.user_id = user.id', [])
            ->group(
                new Expression('SUBSTRING(activity.date,1,'.$interval.')')
            )
            ->where(['user_role.role_id <> ? ' => ModelRole::ROLE_ADMIN_ID])
            ->where(['object_name is not NULL'])
            ->where(["object_name LIKE 'lms.page%'"])
            ->where(["event = 'navigation'"]);

       

        if (null != $start_date) {
            $select->where(['date >= ? ' => $start_date]);
        }

        if (null != $end_date) {
            $select->where(['date <= ? ' => $end_date]);
        }
        
        
        if(null !== $page_id){
           $select->where->in(new Expression('SUBSTRING_INDEX(SUBSTRING_INDEX(object_data, \'"id":"\', \'-1\'), \'"\', 1)'), $page_id);
        }
        return $this->selectWith($select);
    }
    
     public function getDocumentsOpeningCount($me, $interval, $start_date = null, $end_date = null, $page_id = null)
    {
        $select = $this->tableGateway->getSql()->select();
     
            $select->columns([ 
                'activity$date' => new Expression('SUBSTRING(activity.date,1,'.$interval.')'), 
                'event', 
                'activity$count' => new Expression('COUNT(DISTINCT SUBSTRING(activity.date,1,10), library.id, activity.user_id, activity.event)')
                ]
             )
            ->join('user', 'activity.user_id = user.id', [])
            ->join('user_role', 'user_role.user_id = user.id', [])
            ->join('library', new Expression('activity.object_id = library.id'), ['activity$id' => 'id'])
            ->group(
                new Expression('event, library.id, SUBSTRING(activity.date,1,'.$interval.')')
            )
            ->where(['user_role.role_id <> ? ' => ModelRole::ROLE_ADMIN_ID])
            ->where(["event IN ('document.open', 'document.download')"])
            ->order(['event']);

        if (null != $start_date) {
            $select->where(['date >= ? ' => $start_date]);
        }

        if (null != $end_date) {
            $select->where(['date <= ? ' => $end_date]);
        }
        
        if(null !== $page_id){
           $select->join('page_doc', 'library.id = page_doc.library_id',[], $select::JOIN_LEFT)
                  ->join('item', 'library.id = item.library_id',[], $select::JOIN_LEFT)
                  ->where->NEST->in('item.page_id',$page_id)->OR
                  ->in('page_doc.page_id',$page_id)->UNNEST;
        }
        
        return $this->selectWith($select);
    }
    
     public function getDocumentsOpeningPrc($start_date = null, $end_date = null, $page_id = null, $library_id = null)
    {
        $users_select = new Select('page_user');
        $users_select->columns(['count' => new Expression('COUNT(DISTINCT page_user.user_id)')])
            ->where(['page_user.state = ?' => ModelPageUser::STATE_MEMBER])
            ->where->in('page_user.page_id', $page_id);
        
        $select = $this->tableGateway->getSql()->select();
        $select->columns([ 'activity$prc' => new Expression('100 * COUNT(DISTINCT activity.user_id) / users.count')])
            ->join(['users' => $users_select], new Expression('1'))
            ->join('library', new Expression('activity.object_id = library.id'), ['activity$id' => 'id'], $select::JOIN_LEFT)
            ->where('event IN ("document.open", "document.download")')
            ->group('library.id');
           
       if (null != $start_date) {
            $select->where(['date >= ? ' => $start_date]);
        }

        if (null != $end_date) {
            $select->where(['date <= ? ' => $end_date]);
        }
        
        if(null !== $page_id){
           $select->join('page_doc', 'library.id = page_doc.library_id',[], $select::JOIN_LEFT)
                  ->join('item', 'library.id = item.library_id',[], $select::JOIN_LEFT)
                  ->where->NEST->in('item.page_id',$page_id)->OR
                  ->in('page_doc.page_id',$page_id)->UNNEST;
        }
        
        return $this->selectWith($select);
    }
}
<?php

namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;
use Zend\Db\Sql\Predicate\Expression;
use Application\Model\Page as ModelPage;
use Zend\Db\Sql\Predicate\Predicate;

class PostLike extends AbstractMapper
{
    
    public function getCount($me, $interval, $start_date = null, $end_date = null, $page_id = null)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns([ 'post_like$created_date' => new Expression('SUBSTRING(post_like.created_date,1,'.$interval.')'), 'post_like$count' => new Expression('COUNT(DISTINCT post_like.id)')])
            ->group(new Expression('SUBSTRING(post_like.created_date,1,'.$interval.')'));

        if (null != $start_date) {
            $select->where(['post_like.created_date >= ? ' => $start_date]);
        }

        if (null != $end_date) {
            $select->where(['post_like.created_date <= ? ' => $end_date]);
        }
        
        if (null != $page_id) {
            $select->join('user', 'post_like.user_id = user.id', [])
                ->join('page_user', 'user.id = page_user.user_id', [], $select::JOIN_LEFT)
                ->join('page', 'page.id = page_user.page_id',[])
                ->where(['((page_user.page_id = ? ' => $page_id])
                ->where([' page.type <> ? )' => ModelPage::TYPE_ORGANIZATION] )
                ->where([' user.organization_id = ?)' => $page_id], Predicate::OP_OR);
        }
        
        return $this->selectWith($select);
    }
}

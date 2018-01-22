<?php

namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;
use Application\Model\PageUser as ModelPageUser;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Predicate\Predicate;

class PageUser extends AbstractMapper
{
    public function getList($page_id = null, $user_id = null, $role = null, 
        $state = null, $type = null, $me = null, $sent = null, $is_pinned = null, 
        $search = null, $order = null)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['page_id','user_id','state','role'])
            ->join('page', 'page_user.page_id = page.id', [])
            ->join('user', 'page_user.user_id = user.id', [])
            ->where(['page.deleted_date IS NULL'])
            ->where(['user.deleted_date IS NULL'])
            ->quantifier('DISTINCT');

        if (null!==$role) {
            if ($role !== ModelPageUser::ROLE_ADMIN) {
                $select->where(['page_user.state' => ModelPageUser::STATE_MEMBER]);
            } else {
                $select->where(['page_user.state <> ?' => ModelPageUser::STATE_REJECTED])
                    ->order(new Expression('IF(page_user.state = "'.ModelPageUser::STATE_PENDING.'", 0, 1)'));
            }
            $select->where(['page_user.role' => $role]);
        }
        if (null!==$page_id) {
            $select->where(['page_user.page_id' => $page_id]);
        }
        if (null!==$user_id) {
            $select->where(['page_user.user_id' => $user_id]);
        }
        if (null!==$state) {
            $select->where(['page_user.state' => $state]);
        }
        if (null!==$type) {
            $select->where(['page.type' => $type]);
        }
        if(null !== $sent) {
            $select->where(['user.email_sent' => $sent]);
        }
        if(true === $is_pinned) {
            $select->where('page_user.is_pinned IS TRUE');
        }
        else if(false === $is_pinned) {
            $select->where('page_user.is_pinned IS FALSE');
        }
        if (null !== $search) {
            $select->where(['( CONCAT_WS(" ", user.firstname, user.lastname) LIKE ? ' => $search.'%'])
                ->where(['CONCAT_WS(" ", user.lastname, user.firstname) LIKE ? ' => $search.'%'], Predicate::OP_OR)
                ->where(['user.email LIKE ? ' => $search.'%'], Predicate::OP_OR)
                ->where(['user.nickname LIKE ? )' => $search.'%'], Predicate::OP_OR);
        }
        if (null !== $me) {
            $select->join(['me' => 'page_user'], new Expression('me.page_id = page.id AND me.user_id = ?',$me), [], $select::JOIN_LEFT)
                ->where(['( page_user.state NOT IN ("pending", "invited") OR me.role = "admin")'])
                ->where(['( me.role IS NOT NULL OR page.confidentiality=0 ) '])
                ->where(['(me.role = "admin" OR user.is_active = 1)'])
                ->where(['( page.is_published IS TRUE OR page.type <> "course" OR me.role = "admin" )']);
        }
        if (null !== $order) {
            switch ($order['type']) {
            case 'name':
                $select->order(new Expression('COALESCE(user.nickname,TRIM(CONCAT_WS(" ",user.lastname,user.firstname)), user.email)'));
                break;
            case 'firstname':
                $select->order('user.firstname ASC');
                break;
            case 'random':
                $select->order(new Expression('RAND(?)', $order['seed']));
                break;
            default:
                $select->order(['user.id' => 'DESC']);
            }
        }
        return $this->selectWith($select);
    }
}

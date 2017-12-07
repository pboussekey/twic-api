<?php

namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;
use Application\Model\PageUser as ModelPageUser;
use Zend\Db\Sql\Predicate\Expression;

class PageUser extends AbstractMapper
{
    public function getList($page_id = null, $user_id = null, $role = null, $state = null, $type = null, $me = null, $sent = null)
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
        if(null !== $sent){
            $select->where(['user.email_sent' => $sent]);
        }
        if (null !== $me) {
            $select->join(['pu' => 'page_user'], 'pu.page_id = page.id', [], $select::JOIN_LEFT)
            ->where([' ( pu.user_id = ? OR page.confidentiality=0 ) ' => $me])
            ->where(['(pu.role = "admin" OR user.is_active = 1)'])
            ->where(['( page.is_published IS TRUE OR page.type <> "course" OR ( pu.role = "admin" AND pu.user_id = ? ) )' => $me]);
        }
        return $this->selectWith($select);
    }
}

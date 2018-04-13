<?php

namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;
use Zend\Db\Sql\Predicate\Predicate;
use Zend\Db\Sql\Predicate\Expression;

class Subscription extends AbstractMapper
{
    
    public function getListUserId($libelle, $search = null, $order = null){
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['user_id'])
            ->where(['subscription.libelle' => $libelle])
            ->quantifier('DISTINCT');
        
        if(null !== $search || null !== $order){
            $select
                ->join('user', 'subscription.user_id = user.id', []);
        }
        if (null !== $search) {
            $select->where(['( CONCAT_WS(" ", user.firstname, user.lastname) LIKE ? ' => ''.$search.'%'])
                ->where(['CONCAT_WS(" ", user.lastname, user.firstname) LIKE ? ' => ''.$search.'%'], Predicate::OP_OR)
                ->where(['user.email LIKE ? ' => '%'.$search.'%'], Predicate::OP_OR)
                ->where(['user.initial_email LIKE ? ' => '%'.$search.'%'], Predicate::OP_OR)
                ->where(['user.nickname LIKE ? )' => ''.$search.'%'], Predicate::OP_OR);
        }
        
        if (null !== $order && isset($order['type'])) {
            switch ($order['type']) {
            case 'name':
                $select->order(new Expression('user.is_active DESC, COALESCE(NULLIF(user.nickname,""),TRIM(CONCAT_WS(" ",user.lastname,user.firstname, user.email)))'));
                break;
            case 'organization':
                $select
                    ->join(['organization' => 'page'], 'organization.id = user.organization_id', [])
                    ->order( new Expression('organization.title ASC, user.is_active DESC, COALESCE(NULLIF(user.nickname,""),TRIM(CONCAT_WS(" ",user.lastname,user.firstname, user.email)))'));
                break;
            }
         }
        return $this->selectWith($select);

    }
}

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

        $select->join('user', 'subscription.user_id = user.id', ['firstname', 'lastname', 'email', 'initial_email'])
              ->where(['user.deleted_date IS NULL AND user.is_active = 1']);
        if (null !== $search) {
          $tags = explode(' ', $search);
          $select->join('user_tag', 'user_tag.user_id = subscription.user_id', [], $select::JOIN_LEFT)
              ->join('tag', 'user_tag.tag_id = tag.id', [], $select::JOIN_LEFT)
              ->where(['( CONCAT_WS(" ", user.firstname, user.lastname) LIKE ? ' =>  $search . '%'])
              ->where(['CONCAT_WS(" ", user.lastname, user.firstname) LIKE ? ' => $search.'%'], Predicate::OP_OR)
              ->where->OR->in(new Expression('CONCAT( "\'", RIGHT(user.graduation_year, 2))'), $tags);
          $select->where(['user.email LIKE ? ' => $search.'%'], Predicate::OP_OR)
          ->where(['user.initial_email LIKE ? ' => $search.'%'], Predicate::OP_OR)
          ->where(['tag.name'   => $tags], Predicate::OP_OR)
          ->where(['1)'])
          ->having(['( COUNT(DISTINCT tag.id) = ? OR COUNT(DISTINCT tag.id) = 0 ' => count($tags)])
          ->having([' CONCAT_WS(" ", user.firstname, user.lastname) LIKE ? ' => $search . '%'], Predicate::OP_OR)
          ->having(['CONCAT_WS(" ", user.lastname, user.firstname) LIKE ? ' => $search.'%'], Predicate::OP_OR);
          $select->having(['user.email LIKE ? ' => $search.'%'], Predicate::OP_OR)
          ->having(['user.initial_email LIKE ? )' => $search.'%'], Predicate::OP_OR)
          ->group('subscription.user_id');
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

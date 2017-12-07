<?php

namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;
use Zend\Db\Sql\Predicate\Predicate;
use Zend\Db\Sql\Predicate\Expression;

class PostSubscription extends AbstractMapper
{
    public function getLast($post_id, $user_id)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns([
          'action',
          'post_subscription$last_date' => new Expression('DATE_FORMAT(post_subscription.last_date, "%Y-%m-%dT%TZ")'),
          'sub_post_id',
          'user_id',
          'data'])
            ->join('subscription', 'subscription.libelle=post_subscription.libelle', [], $select::JOIN_LEFT)
            ->join('post', 'post.id=post_subscription.sub_post_id', ['id', 'content', 'page_id'], $select::JOIN_LEFT)
            ->where(['( subscription.user_id = ? ' => $user_id])
            ->where(['  post_subscription.libelle = ? )' => 'M'.$user_id], Predicate::OP_OR)
            ->where(['post_subscription.post_id' => $post_id])
            ->order(['post_subscription.id' => 'DESC'])
            ->limit(1);

        return $this->selectWith($select);
    }

    public function getLastLite($post_id, $user_id)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['action',
        'post_subscription$last_date' => new Expression('DATE_FORMAT(post_subscription.last_date, "%Y-%m-%dT%TZ")'),
         'sub_post_id', 'user_id', 'data'])
            ->join('subscription', 'subscription.libelle=post_subscription.libelle', [], $select::JOIN_LEFT)
            ->join('post', 'post.id=post_subscription.sub_post_id', ['id', 'content', 'page_id'], $select::JOIN_LEFT)
            ->where(['( subscription.user_id = ? ' => $user_id])
            ->where(['  post_subscription.libelle = ? )' => 'M'.$user_id], Predicate::OP_OR)
            ->where(['post_subscription.post_id' => $post_id])
            ->order(['post_subscription.id' => 'DESC'])
            ->limit(1);

        return $this->selectWith($select);
    }

    public function getListLibelle($post_id)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['libelle'])
            ->where(['post_subscription.post_id' => $post_id])
            ->quantifier('DISTINCT');

        return $this->selectWith($select);
    }
}

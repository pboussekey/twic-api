<?php

namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;
use Zend\Db\Sql\Predicate\Expression;
use Dal\Db\Sql\Select;
use Zend\Db\Sql\Predicate\Predicate;

class Post extends AbstractMapper
{
    public function getListId($me_id, $page_id = null, $user_id = null, $parent_id = null, $is_item = null, $is_admin = false)
    {
        $select = $this->tableGateway->getSql()->select();
        $columns = ['post$id' => new Expression('post.id')];
        if ($user_id === null && $parent_id === null && $page_id === null) {
            $columns['post$last_date'] = new Expression('DATE_FORMAT(MAX(post_subscription.last_date), "%Y-%m-%dT%TZ")');
            $select->order(['post$last_date' => 'DESC', 'post.id' => 'DESC']);
        } else {
            $select->order(['post.id' => 'DESC']);
        }

        $select->columns($columns);
        $select->join('page', 'page.id = post.t_page_id', [], $select::JOIN_LEFT)
            ->join('post_subscription', 'post_subscription.post_id=post.id', [], $select::JOIN_LEFT)
            ->join('post_user', 'post_user.post_id=post.id', [], $select::JOIN_LEFT)
            ->where(['(post_user.user_id = ? AND post_user.hidden = 0' => $me_id])
            ->where(['  post_user.user_id IS NULL ) '], Predicate::OP_OR)
            ->where(['post.deleted_date IS NULL'])
            ->where(['page.deleted_date IS NULL'])
            ->where(['post.type <> "submission"'])
            ->group('post.id')
            ->quantifier('DISTINCT');

        // @TODO on part du principe que si il n'y a pas de page_id donc c pour un mur donc on récupére que les post des page publish de type course
        // sinon si on donne la page_id on considére qui a pu récupérer l'id donc c accéssible (normalement que pour les admins de la page et les admins studnet)
        if (null === $page_id) {
            $select->where(['( page.is_published IS TRUE OR page.type <> "course" OR page.type IS NULL)']);
        }

        $select->join('item', 'post.item_id = item.id', [], $select::JOIN_LEFT)
          ->where(['( item.id IS NULL OR (item.is_published=true AND
          (`item`.`is_available`=1 OR (`item`.`is_available`=3 AND  (
          ( `item`.`start_date` IS NULL AND `item`.`end_date` IS NULL ) OR
          ( `item`.`start_date` < UTC_TIMESTAMP() AND `item`.`end_date` IS NULL ) OR
          ( `item`.`start_date` IS NULL AND `item`.`end_date` > UTC_TIMESTAMP() ) OR
          ( UTC_TIMESTAMP() BETWEEN `item`.`start_date` AND `item`.`end_date` ))))) )']);

        if (true === $is_item) {
            $select->where(['item.id IS NOT NULL']);
        }
        if (null !== $parent_id) {
            $select->where(['post.parent_id' => $parent_id]);
        } else if($is_admin !== true){
            $select->join('subscription', 'subscription.libelle=post_subscription.libelle', [], $select::JOIN_LEFT)
                ->where(['(subscription.user_id = ? ' => $me_id])
                ->where(['  post_subscription.libelle = ? ) ' => 'M'.$me_id], Predicate::OP_OR)
                ->where(['post.parent_id IS NULL']);
        }
        
        // si c un admin studnet on enleve les type notifs les notif on tous des uid
        if($is_admin === true && null === $parent_id) {
            $select->join('subscription', 'subscription.libelle=post_subscription.libelle', [], $select::JOIN_LEFT)
                ->where(['( ( post.uid IS NOT NULL AND (subscription.user_id = ? ' => $me_id])
                ->where(['  post_subscription.libelle = ?) ) OR post.uid IS NULL ) ' => 'M'.$me_id], Predicate::OP_OR)
                ->where(['post.parent_id IS NULL']);
        }
        if (null !== $user_id) {
            $select->where(['post.t_user_id' => $user_id]);
        }
        
        if (null !== $page_id) {
            $select->where(['post.t_page_id' => $page_id]);
        }

        return $this->selectWith($select);
    }

    public function get($me_id, $id, $is_sadmin = false)
    {
        $select = $this->tableGateway->getSql()->select();

        $nbr_comments = $this->tableGateway->getSql()->select();
        $nbr_comments->columns(['nbr_comments' => new Expression('COUNT(true)')])->where(['post.parent_id=`post$id` AND post.deleted_date IS NULL']);
        $nbr_likes = new Select('post_like');
        $nbr_likes->columns(['nbr_likes' => new Expression('COUNT(true)')])->where(['post_like.post_id=`post$id` AND post_like.is_like IS TRUE']);
        $is_liked = new Select('post_like');
        $is_liked->columns(['is_liked' => new Expression('COUNT(true)')])->where(['post_like.post_id=`post$id` AND post_like.is_like IS TRUE AND post_like.user_id=?' => $me_id]);

        $select->columns([
            'post$id' => new Expression('post.id'),
            'content',
            'link',
            'picture',
            'name_picture',
            'link_title',
            'link_desc',
            'user_id',
            'page_id',
            't_user_id',
            't_page_id',
            'parent_id',
            'type',
            'data',
            'item_id',
            'post$created_date' => new Expression('DATE_FORMAT(post.created_date, "%Y-%m-%dT%TZ")'),
            'post$updated_date' => new Expression('DATE_FORMAT(post.updated_date, "%Y-%m-%dT%TZ")'),
            'post$nbr_comments' => $nbr_comments,
            'post$is_liked' => $is_liked,
            'post$nbr_likes' => $nbr_likes,
        ]);
        $select->where(['post.id' => $id])
            ->join('page', 'page.id = post.t_page_id', [], $select::JOIN_LEFT)
            ->where(['post.deleted_date IS NULL'])
            ->where(['page.deleted_date IS NULL'])
            ->order([ 'post.id' => 'DESC']);


        if (!$is_sadmin) {
            $select->where(['post.deleted_date IS NULL']);
        }

        return $this->selectWith($select);
    }
    
    
    
     public function getCount($me, $interval, $start_date = null, $end_date = null, $organization_id = null, $parent = null){
        $select = $this->tableGateway->getSql()->select();
        $select->columns([ 'post$created_date' => new Expression('SUBSTRING(post.created_date,1,'.$interval.')'), 'post$count' => new Expression('COUNT(DISTINCT post.id)'), 'post$parent_id' => new Expression('IF(post.parent_id IS NOT NULL,1,0)')])
            ->where('post.deleted_date IS NULL')
            ->where('post.uid IS NULL')
            ->group([new Expression('SUBSTRING(post.created_date,1,'.$interval.')'),  new Expression('IF(post.parent_id IS NOT NULL,1,0)') ]);

        if (null != $start_date)
        {
            $select->where(['post.created_date >= ? ' => $start_date]);
        }

        if (null != $end_date)
        {
            $select->where(['post.created_date <= ? ' => $end_date]);
        }
        
        if (null != $organization_id)
        {
            $select->join('user', 'post.user_id = user.id', [])
                ->join('page_user', 'user.id = page_user.user_id', [])
                ->where(['page_user.page_id' => $organization_id]);
        }

        
        if(0 === $parent){
            $select->where('post.parent_id IS NULL');
        }
        else if(1 === $parent){
            $select->where('post.parent_id IS NOT NULL');
        }
        return $this->selectWith($select);
    }
}

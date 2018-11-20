<?php

namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;
use Zend\Db\Sql\Predicate\Expression;
use Dal\Db\Sql\Select;
use Zend\Db\Sql\Predicate\Predicate;
use Application\Model\Page as ModelPage;
use Application\Model\Role as ModelRole;

class Post extends AbstractMapper
{
    public function getListId($me_id, $page_id = null, $user_id = null, $parent_id = null, $is_item = null, $is_admin = false, $type = null)
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
        $select->join('page', new Expression('page.id = post.t_page_id'), [], $select::JOIN_LEFT)
            ->join('page_user', new Expression('page.id = page_user.page_id AND page_user.user_id = ? ', $me_id), [], $select::JOIN_LEFT)
            ->join(['b_page' => 'page'], new Expression('b_page.id = post.page_id'), [], $select::JOIN_LEFT)
            ->join(['b_page_user' => 'page_user'], new Expression('b_page.id = b_page_user.page_id AND b_page_user.user_id = ? ', $me_id), [], $select::JOIN_LEFT)
            ->join('post_subscription', 'post_subscription.post_id=post.id', [], $select::JOIN_LEFT)
            ->join('post_user', new Expression('post_user.post_id=post.id AND post_user.user_id = ?', $me_id), [], $select::JOIN_LEFT)
            ->join('user','post.user_id = user.id', [], $select::JOIN_LEFT)
            ->where(['(post_user.user_id = ? AND post_user.hidden = 0' => $me_id])
            ->where(['  post_user.user_id IS NULL ) '], Predicate::OP_OR)
            ->where(['post.deleted_date IS NULL'])
            ->where(['page.deleted_date IS NULL'])
            ->where(['b_page.deleted_date IS NULL'])
            ->where(['post.type <> "submission"'])
            ->where(['(user.id IS NULL or user.deleted_date IS NULL)'])
            ->group('post.id')
            ->quantifier('DISTINCT');


        $select->join('item', 'post.item_id = item.id', [], $select::JOIN_LEFT)
            ->where(
                ['( item.id IS NULL OR (item.is_published=true AND
          (`item`.`is_available`=1 OR (`item`.`is_available`=3 AND  (
          ( `item`.`start_date` IS NULL AND `item`.`end_date` IS NULL ) OR
          ( `item`.`start_date` < UTC_TIMESTAMP() AND `item`.`end_date` IS NULL ) OR
          ( `item`.`start_date` IS NULL AND `item`.`end_date` > UTC_TIMESTAMP() ) OR
          ( UTC_TIMESTAMP() BETWEEN `item`.`start_date` AND `item`.`end_date` ))))) )']
            );

        if (true === $is_item) {
            $select->where(['item.id IS NOT NULL']);
        }
        if (null !== $parent_id) {
            $select->where(['post.parent_id' => $parent_id]);
        } else if($is_admin !== true) {
            $select->join('subscription', 'subscription.libelle=post_subscription.libelle', [], $select::JOIN_LEFT)
                ->where(['(subscription.user_id = ? ' => $me_id])
                ->where(['  post_subscription.libelle = ? ) ' => 'M'.$me_id], Predicate::OP_OR)
                ->where(['post.parent_id IS NULL'])
                ->where(['( b_page.id IS NULL OR b_page.confidentiality = 0 '])
                ->where([' ((b_page.type <> "course" OR b_page.is_published IS TRUE OR b_page_user.role = "admin") AND b_page_user.user_id IS NOT NULL AND b_page_user.state NOT IN ("pending", "invited")))'], Predicate::OP_OR)
                ->where(['( page.id IS NULL OR page.confidentiality = 0 '])
                ->where([' ((page.type <> "course" OR page.is_published IS TRUE OR page_user.role = "admin") AND page_user.user_id IS NOT NULL AND page_user.state NOT IN ("pending", "invited")))'], Predicate::OP_OR);
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

        if (null !== $type) {
            $select->where(['post.type' => $type]);
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
        $nbr_sharings = new Select('post');
        $nbr_sharings->columns(['nbr_sharings' => new Expression('COUNT(DISTINCT post.user_id)')])->where(['post.shared_id=`post$id` AND post.deleted_date IS NULL']);
        $is_liked = new Select('post_like');
        $is_liked->columns(['is_liked' => new Expression('COUNT(true)')])->where(['post_like.post_id=`post$id` AND post_like.is_like IS TRUE AND post_like.user_id=?' => $me_id]);
        $nbr_views = new Select('post_user');
        $nbr_views->columns(['nbr_views' => new Expression('COUNT(DISTINCT post_user.user_id) + 1')])
                  ->where(new Expression('post_user.post_id=`post$id` AND post_user.view_date IS NOT NULL AND post_user.user_id <> ?', $me_id));
        if(!$is_sadmin){
            $nbr_views->join('user', 'user.id = post_user.user_id', [])
            ->join(['co' => 'circle_organization'], 'co.organization_id=user.organization_id', [])
            ->join('circle_organization', 'circle_organization.circle_id=co.circle_id', [])
            ->join(['circle_organization_user' => 'user'], 'circle_organization_user.organization_id=circle_organization.organization_id', [])
            ->where(['circle_organization_user.id = ?' => $me_id]);
        }

        $select->columns(
            [
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
            'shared_id',
            'post$created_date' => new Expression('DATE_FORMAT(post.created_date, "%Y-%m-%dT%TZ")'),
            'post$updated_date' => new Expression('DATE_FORMAT(post.updated_date, "%Y-%m-%dT%TZ")'),
            'post$nbr_comments' => $nbr_comments,
            'post$is_liked' => $is_liked,
            'post$nbr_likes' => $nbr_likes,
            'post$nbr_sharings' => $nbr_sharings,
            'post$nbr_views' => $nbr_views,
            ]
        );
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

    public function getPostInfos($id){
          $select = $this->tableGateway->getSql()->select();
          $select->columns([
            'id',
            'post$content' => new Expression('COALESCE(post.content, post_parent.content, post_origin.content)'),
            'post$picture' => new Expression('COALESCE(post.picture,  post_parent.picture, post_origin.picture)'),
            'post$name_picture' => new Expression('COALESCE(post.name_picture, post_parent.name_picture, post_origin.name_picture)'),
            'post$link' => new Expression('COALESCE(post.link, post_parent.link, post_origin.link)'),
            'post$type' => new Expression('CASE WHEN post.parent_id IS NULL THEN "post" WHEN post.parent_id = post.origin_id THEN "comment" ELSE "reply" END')
          ])

          ->join(['post_parent' => 'post'],  new Expression('COALESCE(post.shared_id, post.parent_id) = post_parent.id'), ['id'], $select::JOIN_LEFT)
          ->join(['post_origin' => 'post'], 'post.origin_id = post_origin.id', ['id'], $select::JOIN_LEFT)

          ->join(['post_user' => 'user'], 'post.user_id = post_user.id', ['id', 'firstname', 'lastname', 'avatar'], $select::JOIN_LEFT)
          ->join(['post_parent_user' => 'user'], 'post_parent.user_id = post_parent_user.id', ['id', 'firstname', 'lastname', 'avatar'], $select::JOIN_LEFT)

          ->join(['post_page' => 'page'], 'post.page_id = post_page.id', ['id', 'title', 'logo'], $select::JOIN_LEFT)
          ->join(['post_parent_page' => 'page'], 'post_parent.page_id = post_parent_page.id', ['id', 'title', 'logo'], $select::JOIN_LEFT)
          ->join(['post_origin_page' => 'page'], new Expression('COALESCE(post.t_page_id, post_parent.t_page_id, post_origin.t_page_id) = post_origin_page.id'), ['id', 'title', 'logo'], $select::JOIN_LEFT)
          ->where(['post.id' => $id]);

          return $this->selectWith($select);
    }


    public function getCount($me, $interval, $start_date = null, $end_date = null, $page_id = null, $parent = null, $date_offset = 0)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns([ 'post$created_date' => new Expression('SUBSTRING(DATE_SUB(post.created_date, INTERVAL '.$date_offset.' HOUR) ,1,'.$interval.')'), 'post$count' => new Expression('COUNT(DISTINCT post.id)'), 'parent_id' => new Expression('IF(post.parent_id IS NOT NULL,1,0)')])
            ->where('post.deleted_date IS NULL')
            ->where('post.uid IS NULL')
            ->group([new Expression('SUBSTRING(DATE_SUB(post.created_date, INTERVAL '.$date_offset.' HOUR) ,1,'.$interval.')'),  new Expression('IF(post.parent_id IS NOT NULL,1,0)') ]);

        if (null != $start_date) {
            $select->where(['post.created_date >= ? ' => $start_date]);
        }

        if (null != $end_date) {
            $select->where(['post.created_date <= ? ' => $end_date]);
        }
        if (null != $page_id) {
            $select->join('user', 'post.user_id = user.id', [])
                ->join(['parent' => 'post'], 'post.parent_id = parent.id', [], $select::JOIN_LEFT)
                ->join('page', 'page.id = post.t_page_id OR page.id = parent.t_page_id', [], $select::JOIN_LEFT)
                ->where->NEST->NEST->NEST
                ->in('post.t_page_id',$page_id)->OR
                ->in('parent.t_page_id',$page_id)->UNNEST
                ->notEqualTo(' page.type',ModelPage::TYPE_ORGANIZATION )->UNNEST->OR
                ->in(' user.organization_id', $page_id)->UNNEST;
        }

        if(0 === $parent) {
            $select->where('post.parent_id IS NULL');
        }
        else if(1 === $parent) {
            $select->where('post.parent_id IS NOT NULL');
        }
        return $this->selectWith($select);
    }
}

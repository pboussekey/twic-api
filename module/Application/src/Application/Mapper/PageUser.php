<?php

namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;
use Application\Model\PageUser as ModelPageUser;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Predicate\Predicate;

class PageUser extends AbstractMapper
{

    public function get($page_id = null, $user_id = null, $state = null){
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['page_id','user_id','state','role', 'page_user$created_date' => new Expression('DATE_FORMAT(page_user.created_date, "%Y-%m-%dT%TZ")')]);
        if(null !== $page_id){
            $select->where(['page_id' => $page_id]);
        }
        if(null !== $user_id){
            $select->where(['user_id' => $user_id]);
        }

        return $this->selectWith($select);

    }

    public function getList($page_id = null, $user_id = null, $role = null,
        $state = null, $type = null, $me = null, $sent = null, $is_pinned = null,
        $search = null, $order = null, $alumni = null)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['page_id','user_id','state','role'])
            ->join('page', 'page_user.page_id = page.id', [])
            ->join('user', 'page_user.user_id = user.id', ['firstname', 'lastname', 'email', 'initial_email', 'graduation_year'])
            ->where(['page.deleted_date IS NULL'])
            ->where(['user.deleted_date IS NULL'])
            ->quantifier('DISTINCT');

        if (null!==$role) {
            if ($role !== ModelPageUser::ROLE_ADMIN) {
                $select->where(['page_user.state' => ModelPageUser::STATE_MEMBER]);
            } else {
                $select->where(['page_user.state <> ?' => ModelPageUser::STATE_REJECTED]);
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

        if($alumni === true){
            $select->where(['(page.id = user.organization_id AND user.graduation_year < YEAR(CURDATE()))']);
        }
        else if($alumni === false){
            $select->where(['(page.id != user.organization_id OR user.graduation_year >= YEAR(CURDATE()) OR user.graduation_year IS NULL)']);
        }
        if (null !== $search) {

          $tags = explode(' ', trim(preg_replace('/\s+/',' ',preg_replace('/([A-Z][a-z0-9])/',' ${0}', $search))));
          $select->join('user_tag', 'user_tag.user_id = user.id', [], $select::JOIN_LEFT)
              ->join('tag', 'user_tag.tag_id = tag.id', [], $select::JOIN_LEFT)
              ->join('tag_breakdown', 'tag_breakdown.tag_id = tag.id', [], $select::JOIN_LEFT)
              ->where(['( CONCAT_WS(" ", user.lastname, user.firstname) LIKE ? ' =>  $search . '%'])
              ->where(['CONCAT_WS(" ", user.lastname, user.firstname) LIKE ? ' => $search.'%'], Predicate::OP_OR);
          $select->where(['user.email LIKE ? ' => $search.'%'], Predicate::OP_OR)
              ->where(['tag.name'   => $tags], Predicate::OP_OR)
              ->where(['user.initial_email LIKE ? )' => $search.'%'], Predicate::OP_OR)
              ->having(['( COUNT(DISTINCT tag_breakdown.tag_part, tag.id)  = ? OR COUNT(DISTINCT tag.id) = 0 ' => count($tags)])
              ->having([' CONCAT_WS(" ", user.lastname, user.firstname) LIKE ? ' =>  $search . '%'], Predicate::OP_OR)
              ->having(['CONCAT_WS(" ", user.lastname, user.firstname) LIKE ? ' => $search.'%'], Predicate::OP_OR);
          $select->having(['user.email LIKE ? ' => $search.'%'], Predicate::OP_OR)
              ->having(['user.initial_email LIKE ? )' => $search.'%'], Predicate::OP_OR)
              ->group('page_user.user_id');
        }
        if (null !== $me) {
            $select->join(['me' => 'page_user'], new Expression('me.page_id = page.id AND me.user_id = ?',$me), [], $select::JOIN_LEFT)
                ->where(['( page_user.state NOT IN ("pending", "invited") OR me.role = "admin" OR page_user.user_id = me.user_id)'])
                ->where(['( me.role IS NOT NULL OR page.confidentiality<>2 ) '])
                ->where(['(me.role = "admin" OR user.is_active = 1)'])
                ->where(['( page.is_published IS TRUE OR page.type <> "course" OR me.role = "admin" )']);
        }
        if (null !== $order) {
            switch ($order['type']) {
            case 'name':
                $select->order(new Expression('user.is_active DESC, COALESCE(NULLIF(user.nickname,""),TRIM(CONCAT_WS(" ",user.lastname,user.firstname, user.email)))'));
                break;
            case 'firstname':
                $select->order('user.firstname ASC');
                break;
            case 'organization':
                $select
                    ->join(['organization' => 'page'], 'organization.id = user.organization_id', [])
                    ->order( new Expression('organization.title ASC, user.is_active DESC, COALESCE(NULLIF(user.nickname,""),TRIM(CONCAT_WS(" ",user.lastname,user.firstname, user.email)))'));
                break;
            case 'admin':
                $select->order([new Expression('IF(me.role = "admin", 0, 1)'), 'me.page_id DESC']);
                break;
            case 'created_date':
                $select->order(['user.created_date' => 'DESC']);
                break;
            case 'date':
                $select->order([new Expression('IF(page.type = "organization", user.invitation_date, 0) DESC,  page_user.created_date DESC')]);
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

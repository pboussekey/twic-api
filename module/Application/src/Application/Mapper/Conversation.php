<?php

namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Predicate\Predicate;
use Zend\Db\Sql\Predicate\In;
use Zend\Db\Sql\Select;

class Conversation extends AbstractMapper
{

    public function getId($user_id, $contact = null, $noread = null, $type = null, $search = null, $conversation_id = null)
    {
        $select_nb_users = new Select('conversation_user');
        $select_nb_users->columns(['nbr_users' => new Expression('COUNT(true)')])->where(['conversation_user.conversation_id=conversation.id']);

        $subselect = new Select('message');
        $colums  = ['conversation$id' => 'id','type','name','conversation_message$id' => $subselect, 'conversation$nb_users' => $select_nb_users];

        $select = $this->tableGateway->getSql()->select();
        $select->columns($colums)
            ->join('item', 'item.conversation_id=conversation.id', ['conversation$item_id' => 'id'], $select::JOIN_LEFT)
            ->join('page', new Expression("page.conversation_id=conversation.id OR (item.page_id=page.id AND item.participants = 'all')"), ['conversation$page_id' => 'id'], $select::JOIN_LEFT)
            ->where(['page.deleted_date IS NULL'])
            ->where(['( ( page.type = "course" AND page.is_published IS TRUE ) OR page.type <> "course" OR page.type IS NULL )'])
            ->where(['( conversation.type <> 3 OR  item.is_published IS TRUE )'])
            ->order(['conversation_message$id DESC'])
            ->group(['conversation.id']);

        if (null !== $conversation_id) {
            $select->where(['conversation.id' => $conversation_id]);
        } else {
            $select->join('conversation_user', new Expression('conversation.id=conversation_user.conversation_id AND conversation_user.user_id = ?', [$user_id]), [], $select::JOIN_LEFT)
                ->join('page_user', new Expression('page.id=page_user.page_id AND page_user.user_id = ?', [$user_id]), [], $select::JOIN_LEFT)
                ->join('item_user', new Expression('item.id=item_user.item_id AND item_user.user_id = ?', [$user_id]), [], $select::JOIN_LEFT)
                ->where([' ( conversation_user.user_id = ? ' => $user_id])
                ->where(['page_user.user_id = ? ' => $user_id],  Predicate::OP_OR)
                ->where(['item_user.user_id = ? )' => $user_id], Predicate::OP_OR);
        }
        $subselect->columns(['conversation_message$id' => new Expression('MAX(message.id)')])
            ->join('message_user', new Expression('message.id=message_user.message_id AND message_user.user_id = ?', [$user_id]), [], $select::JOIN_LEFT)
            ->join('conversation_user', new Expression('conversation_user.conversation_id=message.conversation_id AND conversation_user.user_id = ?', [$user_id]), [], $select::JOIN_LEFT)   
            ->join('item', 'item.conversation_id=message.conversation_id', [], $select::JOIN_LEFT)
            ->join('page', new Expression("page.conversation_id=message.conversation_id OR (item.page_id=page_id AND item.participants = 'all')"), [], $select::JOIN_LEFT)
            ->join('page_user', new Expression('page.id=page_user.page_id AND page_user.user_id = ?', [$user_id]), [], $select::JOIN_LEFT)
            ->join('item_user', new Expression('item.id=item_user.item_id AND item_user.user_id = ?', [$user_id]), [], $select::JOIN_LEFT)
            ->where([' ( conversation_user.user_id = ? ' => $user_id])
            ->where(['page_user.user_id = ? ' => $user_id],  Predicate::OP_OR)
            ->where(['item_user.user_id = ? )' => $user_id], Predicate::OP_OR)
            ->where(['message_user.deleted_date IS NULL'])
            ->where(['message.conversation_id = conversation.id']);

        if (null !== $search) {
            $searchselect = $this->tableGateway->getSql()->select();
            $searchselect->columns(['id'])
                ->join('conversation_user', 'conversation.id=conversation_user.conversation_id', [])
                ->join('user', 'user.id=conversation_user.user_id', [], $select::JOIN_LEFT)
                ->where(['(conversation.name LIKE ? ' => ''.$search.'%'])
                ->where(['CONCAT_WS(" ", user.firstname, user.lastname) LIKE ? ' => ''.$search.'%'], Predicate::OP_OR)
                ->where(['CONCAT_WS(" ", user.lastname, user.firstname) LIKE ? ' => ''.$search.'%'], Predicate::OP_OR)
                ->where(['user.nickname LIKE ? )' => ''.$search.'%'], Predicate::OP_OR);

            $select->where(['conversation.id IN (?)' => $searchselect]);
        }

        // READ OR NOT READ
        if (true === $noread) {
            $select->where(['conversation_user.read_date IS NOT NULL']);
        }

        // ONLY ONE CONTACT OR NOT
        if (true === $contact || false === $contact) {
            $select->join(['cu' => 'conversation_user'], 'conversation.id=cu.conversation_id', [])
                ->join('contact', new Expression('contact.contact_id=cu.user_id AND contact.user_id = ?', [$user_id]), ['is_contact' => new Expression('IF(contact.deleted_date IS NULL AND contact.accepted_date IS NOT NULL, TRUE, FALSE)')], $select::JOIN_LEFT)
                ->where(['cu.user_id <> ?' => $user_id])
                ->group(['conversation.id']);
            if ($contact) {
                $select->having('COUNT(true) = 1 AND is_contact IS TRUE');
            } else {
                $select->having('!(COUNT(true) = 1 AND is_contact IS TRUE)');
            }
        }

        // TYPE
        if (null !== $type) {
            $select->where(['conversation.type' => $type]);
        }
        
        return $this->selectWith($select);
    }
}

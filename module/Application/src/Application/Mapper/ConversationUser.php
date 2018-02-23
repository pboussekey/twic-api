<?php

namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Having;
use Zend\Db\Sql\Predicate\Expression;

class ConversationUser extends AbstractMapper
{
    /**
     * @param array $users
     * @param int   $type
     * @param int   $submission_id
     *
     * @return ResultSet
     */
    public function getConversationIDByUser($users, $type = null)
    {
        $having = new Having();
        $having->expression('COUNT(1) = ?', count($users));

        $select_sub = $this->tableGateway->getSql()->select();
        $select_sub->columns(array('conversation_id'))
            ->group(array('conversation_id'))
            ->having($having);

        $select = $this->tableGateway->getSql()->select();
        $select->columns(array('conversation_id'))
            ->where(array('conversation_user.user_id' => $users))
            ->where(array('conversation_user.conversation_id IN ? ' => $select_sub))
            ->group(array('conversation_user.conversation_id'))
            ->having($having);

        if (null !== $type) {
            $select->join('conversation', 'conversation.id = conversation_user.conversation_id')->where(array('conversation.type' => $type));
        }

        return $this->selectWith($select);
    }
    
     public function getListByConversation($conversation_id)
    {

        $select = $this->tableGateway->getSql()->select();
       
        $select->columns([
                'conversation_id', 
                'user_id', 
                'last_message'
            ])
            ->where(['conversation_user.conversation_id' => $conversation_id ]);
        
        return $this->selectWith($select);
    }


    public function add($conversation_id, $user)
    {
        $sql = 'INSERT INTO `conversation_user` (`user_id`, `conversation_id`)
      SELECT :u AS `user_id`, :c AS `conversation_id` FROM DUAL
      WHERE NOT EXISTS

      (SELECT `conversation_user`.*
          FROM `conversation_user`
          WHERE `user_id` = :u1 AND `conversation_id` = :c1)';

        return $this->requestPdo($sql, ['u' => $user, 'c' => $conversation_id, 'u1' => $user, 'c1' => $conversation_id]);
    }
}

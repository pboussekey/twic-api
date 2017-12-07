<?php
/**
 * TheStudnet (http://thestudnet.com)
 *
 * Library
 */
namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;

/**
 * Class Library
 */
class Library extends AbstractMapper
{

    /**
     * Get List Library By Page id
     *
     * @return \Dal\Db\ResultSet\ResultSet
     */
    public function getList($folder_id = null, $user_id = null, $page_id = null, $is_admin = false)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['id','name','link','token','type','created_date','deleted_date','updated_date','folder_id','owner_id','box_id', 'text'])
          ->where(['library.deleted_date IS NULL'])
          ->order(['library.id' => 'DESC'])
          ->quantifier('DISTINCT');

        if (null !== $user_id && null === $page_id) {
            $select->where(['library.owner_id' => $user_id]);
        }
        if (null !== $page_id) {
            $select->join('page_doc', 'page_doc.library_id=library.id', [])
            ->where(['page_doc.page_id' => $page_id]);
            if (null !== $user_id && $is_admin === false) {
                $select->join('page_user', 'page_user.page_id=page_doc.page_id', [])->where(['page_user.user_id' => $user_id]);
            }
        }
        if (null !== $folder_id) {
            $select->where(['library.folder_id' => $folder_id]);
        } else {
            $select->where(['library.folder_id IS NULL']);
        }



        return $this->selectWith($select);
    }

    /**
     * Get List Library By Post id
     *
     * @param  int $page_id
     * @return \Dal\Db\ResultSet\ResultSet
     */
    public function getListByPost($post_id)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['id','name','link','token','type','created_date','deleted_date','updated_date','folder_id','owner_id','box_id'])
            ->join('post_doc', 'post_doc.library_id=library.id', [])
            ->where(['post_doc.post_id' => $post_id]);

        return $this->selectWith($select);
    }

    /**
     * Get List Library By Bank Question
     *
     * @param  int $bank_question_id
     * @return \Dal\Db\ResultSet\ResultSet
     */
    public function getListByBankQuestion($bank_question_id)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['id','name','link','token','type','created_date','deleted_date','updated_date','folder_id','owner_id','box_id'])
            ->join('bank_question_media', 'bank_question_media.library_id=library.id', [])
            ->where(['bank_question_media.bank_question_id' => $bank_question_id]);

        return $this->selectWith($select);
    }

    /**
     * Get List Library By Conversation
     *
     * @param  int $conversation_id
     * @return \Dal\Db\ResultSet\ResultSet
     */
    public function getListByConversation($conversation_id)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['id','name','link','token','type','created_date','deleted_date','updated_date','folder_id','owner_id','box_id'])
            ->join('conversation_doc', 'conversation_doc.library_id=library.id', [])
            ->where(['conversation_doc.conversation_id' => $conversation_id]);

        return $this->selectWith($select);
    }
}

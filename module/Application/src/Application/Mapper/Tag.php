<?php

namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;

class Tag extends AbstractMapper
{
    /**
     * Get List Tag By Page
     *
     * @param int $page_id
     */
    public function getListByPage($page_id)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['id', 'name', 'weight'])
            ->join('page_tag', 'page_tag.tag_id=tag.id', [])
            ->where(['page_tag.page_id' => $page_id]);

        return $this->selectWith($select);
    }

    /**
     * Get List
     *
     * @param string $search
     */
    public function getList($search, $exclude = null)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['id', 'name', 'weight'])
          ->where(['name LIKE ? ' => $search . '%'])
          ->order(['weight' => 'DESC']);
        if(null !== $exclude){
          $select->where->notIn('name', $exclude);
        }
        syslog(1, json_encode($select));
        return $this->selectWith($select);
    }

    /**
     * Get List Tag By User
     *
     * @param int $user_id
     */
    public function getListByUser($user_id)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['id', 'name', 'weight'])
            ->join('user_tag', 'user_tag.tag_id=tag.id', [])
            ->where(['user_tag.user_id' => $user_id]);

        return $this->selectWith($select);
    }
}

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
     * @param array|string $category
     * @param array|string $exclude
     */
    public function getList($search, $category = null,  $exclude = null)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['id', 'name', 'weight'])
          ->where(['name LIKE ? ' => $search . '%'])
          ->quantifier('DISTINCT');
        if(null !== $category){
          $select->join('user_tag', 'tag.id = user_tag.tag_id', [])
                 ->where(['user_tag.category' => $category]);
        }
        if(null !== $exclude && count($exclude) > 0){
          $select->where->notIn('name', $exclude);
        }
        return $this->selectWith($select);
    }

    /**
     * Get List Tag By User
     *
     * @param int $user_id
     * @param array|string $category
     */
    public function getListByUser($user_id, $category = null)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['id', 'name', 'weight'])
            ->join('user_tag', 'user_tag.tag_id=tag.id', ['tag$category' => 'category'])
            ->where(['user_tag.user_id' => $user_id]);
        if(null !== $category){
               $select->where(['user_tag.category' => $category]);
        }
        return $this->selectWith($select);
    }
}

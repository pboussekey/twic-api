<?php

namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;
use Zend\Db\Sql\Expression;

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
     * @param array|int $page_id
     */
    public function getList($search, $category = null,  $exclude = null, $page_id = null)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['id', 'name', 'weight'])
          ->where(['name LIKE ? ' => $search . '%'])
          ->quantifier('DISTINCT');
        if(null !== $category || null !== $page_id){
            $select->join('user_tag', 'tag.id = user_tag.tag_id', ['tag$category' => 'category']);
        }
        if(null !== $category){
             $select->where(['user_tag.category' => $category]);
        }
        if(null !== $page_id){
            $select->join('user', 'user.id = user_tag.user_id', [])
                   ->where(['user.organization_id' => $page_id]);
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

    /**
     * Get tag id by name
     *
     * @param string $name
     */
    public function getByName($name)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['id', 'name', 'weight'])
               ->where(['REPLACE(LCASE(name), " ", "") = ? ' => preg_replace('/\s+/', '', strtolower($name))]);
        return $this->selectWith($select);
    }
}

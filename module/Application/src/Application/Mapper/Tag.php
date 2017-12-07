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
}

<?php

namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;

class PageProgram extends AbstractMapper
{
    public function getList($page_id, $search = null)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['id', 'name', 'page_id'])
            ->where(['page_program.page_id' => $page_id]);
       
        if($search !== null) {
            $select->where(['page_program.name LIKE ?' => '%'.$search.'%']);
        }
        
        return $this->selectWith($select);
    }
}
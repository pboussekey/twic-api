<?php

namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;

class PageProgram extends AbstractMapper
{
    public function getList($page_id)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['id', 'name', 'page_id'])
            ->where(['page_program.page_id' => $page_id]);
       
        return $this->selectWith($select);
    }
}
<?php

namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;

class PageProgram extends AbstractMapper
{
    public function getList($page_id = null, $search = null, $user_id = null)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['id', 'name', 'page_id']);
            
        if(null !== $page_id) {
            $select->where(['page_program.page_id' => $page_id]);
        }

        if(null !== $search) {
            $select->where(['page_program.name LIKE ?' => '%'.$search.'%']);
        }
        
        if(null !== $user_id) {
            $select->join('page_program_user', 'page_program_user.page_program_id=page_program.id', [])
                ->where(['page_program_user.user_id' => $user_id]);
        }
        
        return $this->selectWith($select);
    }
}
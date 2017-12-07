<?php

namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;

class Language extends AbstractMapper
{
    public function getList($search = null)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['id', 'libelle']);
        
        if (null !== $search) {
            $select->where(array('libelle LIKE ? ' => ''.$search.'%'));
        }
        
        return $this->selectWith($select);
    }
}

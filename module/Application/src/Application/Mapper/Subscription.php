<?php

namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;

class Subscription extends AbstractMapper
{
    
    public function getListUserId($libelle){
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['user_id'])
            ->where(['libelle' => $libelle])
            ->quantifier('DISTINCT');
        
        return $this->selectWith($select);

    }
}

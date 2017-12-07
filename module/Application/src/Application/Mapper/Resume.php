<?php

namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;

class Resume extends AbstractMapper
{
    public function getListId($user_id)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['id', 'user_id'])
        ->where(['user_id' => $user_id]);

        return $this->selectWith($select);
    }
}

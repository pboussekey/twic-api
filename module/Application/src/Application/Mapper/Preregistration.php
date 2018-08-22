<?php

namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;

class Preregistration extends AbstractMapper
{
    public function get($token)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns([
            'email',
            'firstname',
            'lastname',
            'organization_id',
            'account_token',
            'user_id'
        ])
        ->join('user', 'preregistration.user_id = user.id', [
            'firstname', 
            'lastname', 
            'avatar', 
            'nickname', 
            'is_active',
            'email'
        ], $select::JOIN_LEFT)->where(['preregistration.account_token' => $token]);
        
        return $this->selectWith($select);
    }
}

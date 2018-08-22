<?php

namespace Application\Model;

use Application\Model\Base\Preregistration as BasePreregistration;

class Preregistration extends BasePreregistration
{
    protected $user;
    
    public function exchangeArray(array &$data)
    {
        parent::exchangeArray($data);

        $this->user = $this->requireModel('app_model_user', $data);
    }
    
    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
        
        return $this;
    }

    
}

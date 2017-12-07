<?php

namespace LinkedIn\Model;

class AccessToken extends AbstractModel
{
    protected $access_token;
    protected $expires_in;

    public function getAccessToken()
    {
        return $this->access_token;
    }

    public function setAccessToken($access_token)
    {
        $this->access_token = $access_token;

        return $this;
    }
    
    public function getExpiresIn()
    {
        return $this->expires_in;
    }
    
    public function setExpiresIn($expires_in)
    {
        $this->expires_in = $expires_in;
        
        return $this;
    }
}

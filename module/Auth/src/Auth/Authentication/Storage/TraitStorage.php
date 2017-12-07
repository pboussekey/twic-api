<?php

namespace Auth\Authentication\Storage;

use Zend\Http\Request;

trait TraitStorage
{
    /**
     * @var string
     */
    protected $token;

    /**
     *
     * @var Request
     */
    protected $request;

    /**
     * Token Connexion
     *
     * @return string
     */
    public function getToken()
    {
        if (null===$this->token && null!==$this->request) {
            $aut = $this->request->getHeader('Authorization', null);
            if (null===$aut) {
                $aut = $this->request->getHeader('x-auth-token', null);
            }
            if (null!==$aut) {
                $this->token = $aut->getFieldValue();
            }
        }

        return $this->token;
    }
    
    /**
     * {@inheritDoc}
     * @see \Auth\Authentication\Storage\StorageInterface::setToken()
     */
    public function setToken($token)
    {
        $this->token = $token;
    
        return $this;
    }
    
    /**
     *
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }
}

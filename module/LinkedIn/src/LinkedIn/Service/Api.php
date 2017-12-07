<?php

namespace LinkedIn\Service;

use Zend\Http\Request;
use LinkedIn\Model\AccessToken;
use LinkedIn\Model\People;

class Api extends AbstractApi
{
    /**
     * 
     * @param string $code
     * @param string $access_token
     * @return \LinkedIn\Model\AccessToken
     */
    public function init($code, $access_token = null)
    {
        if (null === $access_token) {
            $this->http_client->getRequest()->setUri('https://www.linkedin.com');
            $this->http_client->getRequest()->getHeaders()->clearHeaders();
            $this->http_client->getRequest()->getHeaders()->addHeaderLine('Content-Type', 'application/x-www-form-urlencoded');
            $this->setMethode(Request::METHOD_POST);
            $this->setPath('/oauth/v2/accessToken');
            $this->setPost([
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $this->redirect_uri,
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
        ]);
            $accessToken = new AccessToken($this->getBody($this->send()));
            $this->access_token = $accessToken->getAccessToken();
        } else {
            $this->access_token = $access_token;
        }
        $this->_init();
        
        return $this->access_token;
    }
    
    /**
     * @return \LinkedIn\Model\People
     */
    public function people()
    {
        $this->setMethode(Request::METHOD_GET);
        $this->setPath(sprintf('/people/~:(id,first-name,last-name,summary,positions,picture-urls::(original),picture-url,public-profile-url)?format=json'));
        
        return new People($this->getBody($this->send()));
    }
}

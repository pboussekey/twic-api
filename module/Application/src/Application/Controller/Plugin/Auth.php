<?php
/**
 * ApiAuth
 */
namespace Application\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Authentication\AuthenticationService as AuthService;

/**
 * Plugin ApiAuth
 */
class ApiAuth extends AbstractPlugin
{

    protected $auth;

    /**
     */
    public function __construct(AuthService $auth)
    {
        $this->auth = $auth;
    }

    public function authenticate($apikey)
    {

        $this->auth->getAdapter()->setApiKey($apikey);

        $code = - 32000;
        $result = $this->auth->authenticate();
        return $result->isValid();

    }


}

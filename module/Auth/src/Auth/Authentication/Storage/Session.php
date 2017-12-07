<?php

/**
 * Zend Framework (http://framework.zend.com/).
 *
 * @link http://github.com/zendframework/zf2 for the canonical source repository
 *
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Auth\Authentication\Storage;

use Application\Session\Container as SessionContainer;
use Zend\Session\ManagerInterface as SessionManager;
use Zend\Authentication\Storage\Session as BaseSession;

class Session extends BaseSession
{
    /**
     * Sets session storage options and initializes session namespace object.
     *
     * @param mixed          $namespace
     * @param mixed          $member
     * @param SessionManager $manager
     */
    public function __construct($namespace = null, $member = null, SessionManager $manager = null)
    {
        if ($namespace !== null) {
            $this->namespace = $namespace;
        }
        if ($member !== null) {
            $this->member = $member;
        }
        $this->session = new SessionContainer($this->namespace, $manager);
    }
}

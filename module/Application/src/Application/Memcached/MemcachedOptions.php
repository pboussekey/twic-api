<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Cache\Storage\Adapter;

use Zend\Cache\Storage\Adapter\MemcachedOptions as BaseMemcachedOptions;

/**
 * These are options specific to the Memcached adapter
 */
class MemcachedOptions extends BaseMemcachedOptions
{
    /**
     * Set the credentials to use for authentication
     * 
     * @param array $auth
     * @return \Application\Cache\Storage\Adapter\MemcachedOptions
     */
    public function setSaslAuthData($credentials)
    {
        $this->getResourceManager()->getResource($this->getResourceId())->setOption(\Memcached::OPT_BINARY_PROTOCOL, true);
        $this->getResourceManager()->getResource($this->getResourceId())->setSaslAuthData($credentials['username'], $credentials['password']);
        
        return $this;
    }
}

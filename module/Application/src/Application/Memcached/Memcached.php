<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Cache\Storage\Adapter;

use Zend\Cache\Storage\Adapter\Memcached as BaseMemcached;

class Memcached extends BaseMemcached
{

    /**
     * Set options.
     *
     * @param  array|\Traversable|MemcachedOptions $options
     * 
     * @return Memcached
     * @see getOptions()
     */
    public function setOptions($options)
    {
        if (! $options instanceof MemcachedOptions) {
            $options = new MemcachedOptions($options);
        }

        return parent::setOptions($options);
    }
}

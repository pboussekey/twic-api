<?php

namespace Auth\Authentication\Storage;

class CacheStorage implements StorageInterface
{
    use TraitStorage;
    /**
     * @var \Zend\Cache\Storage\StorageInterface
     */
    protected $cache;

    protected $prefix_session = 'sess_';
    /**
     * @param \Zend\Cache\Storage\StorageInterface $cache
     */
    public function __construct($cache)
    {
        $this->cache = $cache;
    }

    /**
     * Returns true if and only if storage is empty.
     *
     * @throws \Zend\Authentication\Exception\ExceptionInterface If it is impossible to determine whether storage is empty
     *
     * @return bool
     */
    public function isEmpty()
    {
        if ($this->getToken() && $this->getSession() !== false) {
            return false;
        }

        return true;
    }

    /**
     * Returns the contents of storage.
     *
     * Behavior is undefined when storage is empty.
     *
     * @throws \Zend\Authentication\Exception\InvalidArgumentException
     *
     * @return \Auth\Authentication\Adapter\Model\Identity
     */
    public function read()
    {
        return $this->getSession();
    }

    /**
     * Writes $contents to storage.
     *
     * @param mixed $contents
     *
     * @throws \Zend\Authentication\Exception\InvalidArgumentException If writing $contents to storage is impossible
     */
    public function write($contents)
    {
        $this->setToken($contents->getToken());
        $this->saveSession($contents);

        return;
    }

    /**
     * Clears contents from storage.
     *
     * @throws \Zend\Authentication\Exception\InvalidArgumentException If clearing contents from storage is impossible
     */
    public function clear()
    {
        $ssesion = $this->cache->getItem($this->getPrefixToken());
        $ssesion_user = $this->cache->getItem($ssesion->getId());
        if ($ssesion_user->offsetExists($this->getPrefixToken())) {
            $ssesion_user->offsetUnset($this->getPrefixToken());
        }
        $this->cache->setItem($ssesion->getId(), $ssesion_user);
        $this->cache->removeItem($this->getPrefixToken());

        return;
    }
    
    /**
     * Clears contents from storage for a specific user.
     *
     * @throws \Zend\Authentication\Exception\InvalidArgumentException If clearing contents from storage is impossible
     */
    public function clearSession($uid)
    {
        $session_user = $this->cache->getItem($uid);
        if (null !== $session_user) {
            foreach ($this->cache->getItem($uid) as $key => $session) {
                $session_user->offsetUnset($key);
                $this->cache->removeItem($key);
            }
            $this->cache->setItem($uid, $session_user);
        }

        return;
    }
    
    /**
     * Get token with prefix.
     *
     * @return string
     */
    public function getPrefixToken()
    {
        return sprintf($this->prefix_session.'%s', $this->getToken());
    }

    /**
     * @return false|mixed
     */
    public function getSession()
    {
        if (($ssesion = $this->cache->getItem($this->getPrefixToken())) === null) {
            $ssesion = false;
        }

        return $ssesion;
    }

    public function getListSession($uid)
    {
        return $this->cache->getItem($uid);
    }

    public function saveSession($content)
    {
        if ($this->cache->getItem($this->getPrefixToken()) === null) {
            $this->cache->addItem($this->getPrefixToken(), $content);
        } else {
            $this->cache->setItem($this->getPrefixToken(), $content);
        }

        if ($this->cache->getItem($content->getId()) === null) {
            $this->cache->addItem($content->getId(), new \ArrayObject());
        }

        $objUserArray = $this->cache->getItem($content->getId());
        $objUserArray->offsetSet($this->getPrefixToken(), $content);
        $this->cache->setItem($content->getId(), $objUserArray);

        return true;
    }
}

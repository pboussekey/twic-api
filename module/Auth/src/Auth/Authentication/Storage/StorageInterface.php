<?php

namespace Auth\Authentication\Storage;

use Zend\Authentication\Storage\StorageInterface as BaseStorageInterface;

interface StorageInterface extends BaseStorageInterface
{
    /**
     * @param int $token
     */
    public function setToken($token);
}

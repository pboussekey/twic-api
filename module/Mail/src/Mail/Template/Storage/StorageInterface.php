<?php

namespace Mail\Template\Storage;

interface StorageInterface
{
    /**
     * @param \Mail\Template\Model\TplModel $model
     */
    public function write(\Mail\Template\Model\TplModel $model);

    /**
     * @return \Mail\Template\Model\TplModel
     */
    public function read($name);

    /**
     * @param string $name
     */
    public function exist($name);

    /**
     * get List Tpl.
     */
    public function getList();
    
    /**
     * @param array $config
     */
    public function init($config = []);
}

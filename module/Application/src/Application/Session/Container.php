<?php

namespace Application\Session;

class Container extends AbstractContainer
{
    /**
     * Retrieve a specific key in the container.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function &offsetGet($key)
    {
        $ret = null;
        if (!$this->offsetExists($key)) {
            return $ret;
        }
        $storage = $this->getStorage();
        $name = $this->getName();
        $ret = &$storage[$name][$key];

        return $ret;
    }
}

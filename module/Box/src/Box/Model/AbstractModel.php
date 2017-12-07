<?php

namespace Box\Model;

use JsonSerializable;
use Zend\Hydrator\ClassMethods;

abstract class AbstractModel implements JsonSerializable
{
    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->exchangeArray($data);
    }

    /**
     * Populate from an array.
     *
     * @param array $data
     *
     * @return AbstractModel
     */
    public function exchangeArray(array $data)
    {
        $hydrator = new ClassMethods(false);
        $hydrator->hydrate($data, $this);

        return $this;
    }

    /**
     * Convert the model to an array.
     *
     * @return array
     */
    public function toArray()
    {
        $hydrator = new ClassMethods(false);
        $vars = $hydrator->extract($this);

        foreach ($vars as $key => &$value) {
            if (is_object($value)) {
                if (method_exists($value, 'toArray')) {
                    $value = $value->toArray();
                    if (count($value) == 0) {
                        unset($vars[$key]);
                    }
                } else {
                    unset($vars[$key]);
                }
            } elseif (is_bool($value)) {
                $vars[$key] = (int) $value;
            }
        }

        return $vars;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}

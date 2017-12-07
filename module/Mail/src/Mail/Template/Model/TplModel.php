<?php

namespace Mail\Template\Model;

use Zend\Stdlib\ArrayObject;

class TplModel extends ArrayObject implements \JsonSerializable
{
    protected $name;
    protected $subject;
    protected $from;
    protected $from_name;

    /**
     * @return string $from_name
     */
    public function getFromName()
    {
        return $this->from_name;
    }

    /**
     * @param string $fromName
     */
    public function setFromName($from_name)
    {
        $this->from_name = $from_name;
    }

    public function setFrom($from)
    {
        $this->from = $from;

        return $this;
    }

    public function getFrom()
    {
        return $this->from;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * Unserialize an ArrayObject.
     *
     * @param string $data
     */
    public function unserialize($data)
    {
        $ar = unserialize($data);
        $this->protectedProperties = array_keys(get_object_vars($this));

        foreach ($ar as $k => $v) {
            switch ($k) {
            case 'flag':
                $this->setFlags($v);
                break;
            case 'storage':
                $this->exchangeArray($v);
                break;
            case 'iteratorClass':
                $this->setIteratorClass($v);
                break;
            case 'name':
                $this->setName($v);
                break;
            case 'from_name':
                $this->setFromName($v);
                break;
            case 'from':
                $this->setFrom($v);
                break;
            case 'subject':
                $this->setSubject($v);
                break;
            case 'protectedProperties':
                continue;
            default:
                $this->__set($k, $v);
            }
        }
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}

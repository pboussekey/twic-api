<?php

namespace Box\Model;

class Document extends AbstractModel
{
    protected $id;
    protected $name;
    protected $created_at;
    protected $modified_at;
    protected $status;

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function getModifiedAt()
    {
        return $this->modified_at;
    }

    public function setModifiedAt($modified_at)
    {
        $this->modified_at = $modified_at;

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}

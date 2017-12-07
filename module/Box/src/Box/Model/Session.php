<?php

namespace Box\Model;

class Session extends AbstractModel
{
    protected $id;
    protected $expires_at;
    protected $urls;

    public function getExpiresAt()
    {
        return $this->expires_at;
    }

    public function setExpiresAt($expires_at)
    {
        $this->expires_at = $expires_at;

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

    public function getUrls()
    {
        return $this->urls;
    }

    public function setUrls($urls)
    {
        $this->urls = $urls;

        return $this;
    }
}

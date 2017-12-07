<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class VideoArchive extends AbstractModel
{
    protected $id;
    protected $archive_token;
    protected $archive_link;
    protected $archive_status;
    protected $archive_duration;
    protected $conversation_id;
    protected $created_date;

    protected $prefix = 'video_archive';

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getArchiveToken()
    {
        return $this->archive_token;
    }

    public function setArchiveToken($archive_token)
    {
        $this->archive_token = $archive_token;

        return $this;
    }

    public function getArchiveLink()
    {
        return $this->archive_link;
    }

    public function setArchiveLink($archive_link)
    {
        $this->archive_link = $archive_link;

        return $this;
    }

    public function getArchiveStatus()
    {
        return $this->archive_status;
    }

    public function setArchiveStatus($archive_status)
    {
        $this->archive_status = $archive_status;

        return $this;
    }

    public function getArchiveDuration()
    {
        return $this->archive_duration;
    }

    public function setArchiveDuration($archive_duration)
    {
        $this->archive_duration = $archive_duration;

        return $this;
    }

    public function getConversationId()
    {
        return $this->conversation_id;
    }

    public function setConversationId($conversation_id)
    {
        $this->conversation_id = $conversation_id;

        return $this;
    }

    public function getCreatedDate()
    {
        return $this->created_date;
    }

    public function setCreatedDate($created_date)
    {
        $this->created_date = $created_date;

        return $this;
    }
}

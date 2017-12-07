<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class SubmissionComments extends AbstractModel
{
    protected $id;
    protected $text;
    protected $audio;
    protected $user_id;
    protected $submission_id;
    protected $file_token;
    protected $file_name;
    protected $created_date;
    protected $read_date;

    protected $prefix = 'submission_comments';

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    public function getAudio()
    {
        return $this->audio;
    }

    public function setAudio($audio)
    {
        $this->audio = $audio;

        return $this;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getSubmissionId()
    {
        return $this->submission_id;
    }

    public function setSubmissionId($submission_id)
    {
        $this->submission_id = $submission_id;

        return $this;
    }

    public function getFileToken()
    {
        return $this->file_token;
    }

    public function setFileToken($file_token)
    {
        $this->file_token = $file_token;

        return $this;
    }

    public function getFileName()
    {
        return $this->file_name;
    }

    public function setFileName($file_name)
    {
        $this->file_name = $file_name;

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

    public function getReadDate()
    {
        return $this->read_date;
    }

    public function setReadDate($read_date)
    {
        $this->read_date = $read_date;

        return $this;
    }
}

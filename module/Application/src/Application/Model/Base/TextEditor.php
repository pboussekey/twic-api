<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class TextEditor extends AbstractModel
{
    protected $id;
    protected $name;
    protected $text;
    protected $submit_date;

    protected $prefix = 'text_editor';

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

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

    public function getText()
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    public function getSubmitDate()
    {
        return $this->submit_date;
    }

    public function setSubmitDate($submit_date)
    {
        $this->submit_date = $submit_date;

        return $this;
    }
}

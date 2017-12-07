<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class LanguageLevel extends AbstractModel
{
    protected $id;
    protected $level;

    protected $prefix = 'language_level';

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }
}

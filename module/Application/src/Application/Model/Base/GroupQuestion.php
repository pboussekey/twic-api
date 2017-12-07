<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class GroupQuestion extends AbstractModel
{
    protected $id;
    protected $nb;

    protected $prefix = 'group_question';

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getNb()
    {
        return $this->nb;
    }

    public function setNb($nb)
    {
        $this->nb = $nb;

        return $this;
    }
}

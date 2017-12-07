<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class Language extends AbstractModel
{
    protected $id;
    protected $libelle;

    protected $prefix = 'language';

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getLibelle()
    {
        return $this->libelle;
    }

    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }
}

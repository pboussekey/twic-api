<?php

namespace Rbac\Db\Model\Base;

use Dal\Model\AbstractModel;

class Permission extends AbstractModel
{
    protected $id;

    protected $libelle;

    protected $prefix = 'permission';

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getLibelle()
    {
        return $this->libelle;
    }
}

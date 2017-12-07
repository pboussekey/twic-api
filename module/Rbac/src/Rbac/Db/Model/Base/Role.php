<?php

namespace Rbac\Db\Model\Base;

use Dal\Model\AbstractModel;

class Role extends AbstractModel
{
    const STR_GUEST = 'guest';
    const STR_SUPER_ADMIN = 'super_admin';
    const STR_ADMIN = 'admin';
    const STR_ACADEMIC = 'academic';
    const STR_STUDENT = 'student';
    const STR_INSTRUCTOR = 'instructor';
    const STR_RECRUITER = 'recruiter';

    protected $id;
    protected $name;

    protected $prefix = 'role';

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
}

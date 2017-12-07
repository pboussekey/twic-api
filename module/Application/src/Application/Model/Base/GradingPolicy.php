<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class GradingPolicy extends AbstractModel
{
    protected $id;
    protected $name;
    protected $grade;
    protected $type;
    protected $tpl;
    protected $course_id;
    protected $mandatory;

    protected $prefix = 'grading_policy';

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

    public function getGrade()
    {
        return $this->grade;
    }

    public function setGrade($grade)
    {
        $this->grade = $grade;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function getTpl()
    {
        return $this->tpl;
    }

    public function setTpl($tpl)
    {
        $this->tpl = $tpl;

        return $this;
    }

    public function getCourseId()
    {
        return $this->course_id;
    }

    public function setCourseId($course_id)
    {
        $this->course_id = $course_id;

        return $this;
    }

    public function getMandatory()
    {
        return $this->mandatory;
    }

    public function setMandatory($mandatory)
    {
        $this->mandatory = $mandatory;

        return $this;
    }
}

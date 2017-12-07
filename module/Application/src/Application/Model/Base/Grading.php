<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class Grading extends AbstractModel
{
    protected $id;
    protected $letter;
    protected $min;
    protected $max;
    protected $grade;
    protected $description;
    protected $tpl;
    protected $organization_id;

    protected $prefix = 'grading';

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getLetter()
    {
        return $this->letter;
    }

    public function setLetter($letter)
    {
        $this->letter = $letter;

        return $this;
    }

    public function getMin()
    {
        return $this->min;
    }

    public function setMin($min)
    {
        $this->min = $min;

        return $this;
    }

    public function getMax()
    {
        return $this->max;
    }

    public function setMax($max)
    {
        $this->max = $max;

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

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;

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

    public function getOrganizationId()
    {
        return $this->organization_id;
    }

    public function setOrganizationId($organization_id)
    {
        $this->organization_id = $organization_id;

        return $this;
    }
}

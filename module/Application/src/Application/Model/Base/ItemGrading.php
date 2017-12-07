<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class ItemGrading extends AbstractModel
{
    protected $id;
    protected $item_prog_user_id;
    protected $grade;
    protected $created_date;

    protected $prefix = 'item_grading';

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getItemProgUserId()
    {
        return $this->item_prog_user_id;
    }

    public function setItemProgUserId($item_prog_user_id)
    {
        $this->item_prog_user_id = $item_prog_user_id;

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

<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class OptGrading extends AbstractModel
{
    protected $item_id;
    protected $mode;
    protected $has_pg;
    protected $pg_nb;
    protected $pg_auto;
    protected $pg_due_date;
    protected $pg_can_view;
    protected $user_can_view;
    protected $pg_stars;

    protected $prefix = 'opt_grading';

    public function getItemId()
    {
        return $this->item_id;
    }

    public function setItemId($item_id)
    {
        $this->item_id = $item_id;

        return $this;
    }

    public function getMode()
    {
        return $this->mode;
    }

    public function setMode($mode)
    {
        $this->mode = $mode;

        return $this;
    }

    public function getHasPg()
    {
        return $this->has_pg;
    }

    public function setHasPg($has_pg)
    {
        $this->has_pg = $has_pg;

        return $this;
    }

    public function getPgNb()
    {
        return $this->pg_nb;
    }

    public function setPgNb($pg_nb)
    {
        $this->pg_nb = $pg_nb;

        return $this;
    }

    public function getPgAuto()
    {
        return $this->pg_auto;
    }

    public function setPgAuto($pg_auto)
    {
        $this->pg_auto = $pg_auto;

        return $this;
    }

    public function getPgDueDate()
    {
        return $this->pg_due_date;
    }

    public function setPgDueDate($pg_due_date)
    {
        $this->pg_due_date = $pg_due_date;

        return $this;
    }

    public function getPgCanView()
    {
        return $this->pg_can_view;
    }

    public function setPgCanView($pg_can_view)
    {
        $this->pg_can_view = $pg_can_view;

        return $this;
    }

    public function getUserCanView()
    {
        return $this->user_can_view;
    }

    public function setUserCanView($user_can_view)
    {
        $this->user_can_view = $user_can_view;

        return $this;
    }

    public function getPgStars()
    {
        return $this->pg_stars;
    }

    public function setPgStars($pg_stars)
    {
        $this->pg_stars = $pg_stars;

        return $this;
    }
}

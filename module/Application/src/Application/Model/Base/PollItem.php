<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class PollItem extends AbstractModel
{
    protected $id;
    protected $poll_id;
    protected $bank_question_id;
    protected $group_question_id;
    protected $order_id;
    protected $is_mandatory;
    protected $nb_point;

    protected $prefix = 'poll_item';

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getPollId()
    {
        return $this->poll_id;
    }

    public function setPollId($poll_id)
    {
        $this->poll_id = $poll_id;

        return $this;
    }

    public function getBankQuestionId()
    {
        return $this->bank_question_id;
    }

    public function setBankQuestionId($bank_question_id)
    {
        $this->bank_question_id = $bank_question_id;

        return $this;
    }

    public function getGroupQuestionId()
    {
        return $this->group_question_id;
    }

    public function setGroupQuestionId($group_question_id)
    {
        $this->group_question_id = $group_question_id;

        return $this;
    }

    public function getOrderId()
    {
        return $this->order_id;
    }

    public function setOrderId($order_id)
    {
        $this->order_id = $order_id;

        return $this;
    }

    public function getIsMandatory()
    {
        return $this->is_mandatory;
    }

    public function setIsMandatory($is_mandatory)
    {
        $this->is_mandatory = $is_mandatory;

        return $this;
    }

    public function getNbPoint()
    {
        return $this->nb_point;
    }

    public function setNbPoint($nb_point)
    {
        $this->nb_point = $nb_point;

        return $this;
    }
}

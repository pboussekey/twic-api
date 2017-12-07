<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class QuizQuestion extends AbstractModel
{
    protected $id;
    protected $quiz_id;
    protected $point;
    protected $text;
    protected $type;
    protected $order;

    protected $prefix = 'quiz_question';

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getQuizId()
    {
        return $this->quiz_id;
    }

    public function setQuizId($quiz_id)
    {
        $this->quiz_id = $quiz_id;

        return $this;
    }

    public function getPoint()
    {
        return $this->point;
    }

    public function setPoint($point)
    {
        $this->point = $point;

        return $this;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;

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

    public function getOrder()
    {
        return $this->order;
    }

    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }
}

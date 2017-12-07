<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class ItemComposant extends AbstractModel
{
    protected $item_id;
    protected $has_eqcq;
    protected $has_conversation;
    protected $has_videoconf;
    protected $has_assigment;
    protected $has_poll;
    protected $has_document;

    protected $prefix = 'item_composant';

    public function getItemId()
    {
        return $this->item_id;
    }

    public function setItemId($item_id)
    {
        $this->item_id = $item_id;

        return $this;
    }

    public function getHasEqcq()
    {
        return $this->has_eqcq;
    }

    public function setHasEqcq($has_eqcq)
    {
        $this->has_eqcq = $has_eqcq;

        return $this;
    }

    public function getHasConversation()
    {
        return $this->has_conversation;
    }

    public function setHasConversation($has_conversation)
    {
        $this->has_conversation = $has_conversation;

        return $this;
    }

    public function getHasVideoconf()
    {
        return $this->has_videoconf;
    }

    public function setHasVideoconf($has_videoconf)
    {
        $this->has_videoconf = $has_videoconf;

        return $this;
    }

    public function getHasAssigment()
    {
        return $this->has_assigment;
    }

    public function setHasAssigment($has_assigment)
    {
        $this->has_assigment = $has_assigment;

        return $this;
    }

    public function getHasPoll()
    {
        return $this->has_poll;
    }

    public function setHasPoll($has_poll)
    {
        $this->has_poll = $has_poll;

        return $this;
    }

    public function getHasDocument()
    {
        return $this->has_document;
    }

    public function setHasDocument($has_document)
    {
        $this->has_document = $has_document;

        return $this;
    }
}

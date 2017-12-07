<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class Contact extends AbstractModel
{
    protected $id;
    protected $user_id;
    protected $contact_id;
    protected $request_date;
    protected $deleted_date;
    protected $accepted_date;
    protected $deleted;
    protected $accepted;
    protected $requested;

    protected $prefix = 'contact';

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getContactId()
    {
        return $this->contact_id;
    }

    public function setContactId($contact_id)
    {
        $this->contact_id = $contact_id;

        return $this;
    }

    public function getRequestDate()
    {
        return $this->request_date;
    }

    public function setRequestDate($request_date)
    {
        $this->request_date = $request_date;

        return $this;
    }

    public function getDeletedDate()
    {
        return $this->deleted_date;
    }

    public function setDeletedDate($deleted_date)
    {
        $this->deleted_date = $deleted_date;

        return $this;
    }

    public function getAcceptedDate()
    {
        return $this->accepted_date;
    }

    public function setAcceptedDate($accepted_date)
    {
        $this->accepted_date = $accepted_date;

        return $this;
    }

    public function getDeleted()
    {
        return $this->deleted;
    }

    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function getAccepted()
    {
        return $this->accepted;
    }

    public function setAccepted($accepted)
    {
        $this->accepted = $accepted;

        return $this;
    }

    public function getRequested()
    {
        return $this->requested;
    }

    public function setRequested($requested)
    {
        $this->requested = $requested;

        return $this;
    }
}

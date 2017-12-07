<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class Preregistration extends AbstractModel
{
    protected $email;
    protected $firstname;
    protected $lastname;
    protected $organization_id;
    protected $account_token;
    protected $user_id;

    protected $prefix = 'preregistration';

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

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

    public function getAccountToken()
    {
        return $this->account_token;
    }

    public function setAccountToken($account_token)
    {
        $this->account_token = $account_token;

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
}

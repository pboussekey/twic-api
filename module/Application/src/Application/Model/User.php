<?php

namespace Application\Model;

use Zend\Db\Sql\Predicate\IsNull;
use Application\Model\Base\User as BaseUser;

class User extends BaseUser
{
    protected $roles;
    protected $available;
    protected $selected;
    protected $contact_state;
    protected $contacts_count;
    protected $gender;
    protected $nationality;
    protected $origin;
    protected $role;
    protected $nb_user;
    protected $role_id;
    protected $address;

    public function exchangeArray(array &$data)
    {
        if ($this->isRepeatRelational()) {
            return;
        }

        parent::exchangeArray($data);

        $this->role = $this->requireModel('app_model_role', $data);
        $this->nationality = $this->requireModel('addr_model_country', $data, 'nationality');
        $this->origin = $this->requireModel('addr_model_country', $data, 'origin');
        $this->address = $this->requireModel('addr_model_address', $data);
    }

    public function getRoleId()
    {
        return $this->role_id;
    }

    public function setRoleId($role_id)
    {
        $this->role_id = $role_id;

        return $this;
    }

    public function getNbUser()
    {
        return $this->nb_user;
    }

    public function setNbUser($nb_user)
    {
        $this->nb_user = $nb_user;

        return $this;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    public function getOrigin()
    {
        return $this->origin;
    }

    public function setOrigin($origin)
    {
        $this->origin = $origin;

        return $this;
    }
    public function getNationality()
    {
        return $this->nationality;
    }

    public function setNationality($nationality)
    {
        $this->nationality = $nationality;

        return $this;
    }
    public function getGender()
    {
        return $this->gender;
    }

    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    public function getContactState()
    {
        return $this->contact_state;
    }

    public function setContactState($contact_state)
    {
        $this->contact_state = ($contact_state === null || $contact_state instanceof IsNull) ? 0 : $contact_state;

        return $this;
    }

    public function getContactsCount()
    {
        return $this->contacts_count;
    }

    public function setContactsCount($contacts_count)
    {
        $this->contacts_count = ($contacts_count === null || $contacts_count instanceof IsNull) ? 0 : $contacts_count;

        return $this;
    }

    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function setAvailable($available)
    {
        $this->available = $available;

        return $this;
    }

    public function getAvailable()
    {
        return $this->available;
    }

    public function setSelected($selected)
    {
        $this->selected = $selected;

        return $this;
    }

    public function getSelected()
    {
        return $this->selected;
    }

    /**
     * Get the value of Address
     *
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set the value of Address
     *
     * @param mixed address
     *
     * @return self
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }
}

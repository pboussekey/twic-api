<?php

namespace Auth\Authentication\Adapter\Model;

class Identity implements IdentityInterface
{
    protected $id;
    protected $lastname;
    protected $firstname;
    protected $nickname;
    protected $token;
    protected $created_date;
    protected $expiration_date;
    protected $avatar;
    protected $email;
    protected $linkedin_id;
    protected $organization_id;
    protected $suspension_date;
    protected $suspension_reason;
    protected $has_linkedin;

    /**
     * @return string $has_linkedin
     */
    public function getHasLinkedin()
    {
        return $this->has_linkedin;
    }

    /**
     * @param string $has_linkedin
     */
    public function setHasLinkedin($has_linkedin)
    {
        $this->has_linkedin = $has_linkedin;
        
        return $this;
    }

    public function exchangeArray(array $datas)
    {
        foreach ($datas as $property => $value) {
            $method = str_replace(array('_a', '_b', '_c', '_d', '_e', '_f', '_g', '_h', '_i', '_j', '_k', '_l', '_m', '_n', '_o', '_p', '_q', '_r', '_s', '_t', '_u', '_v', '_w', '_x', '_y', '_z'), array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'), 'set_'.$property);

            if (is_callable(array($this, $method))) {
                $this->$method($value);
            }
        }

        return $this;
    }

    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getAvatar()
    {
        return $this->avatar;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setToken($token)
    {
        $this->token = $token;

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

    public function getExpirationDate()
    {
        return $this->expiration_date;
    }

    public function setExpirationDate($expiration_date)
    {
        $this->expiration_date = $expiration_date;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;

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

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getNickname()
    {
        return $this->nickname;
    }

    public function setNickname($nickname)
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getSuspensionDate()
    {
        return $this->suspension_date;
    }

    public function setSuspensionDate($suspension_date)
    {
        $this->suspension_date = $suspension_date;

        return $this;
    }

    public function getSuspensionReason()
    {
        return $this->suspension_reason;
    }

    public function setSuspensionReason($suspension_reason)
    {
        $this->suspension_reason = $suspension_reason;

        return $this;
    }

    /**
     * Get the value of Organization Id
     *
     * @return mixed
     */
    public function getOrganizationId()
    {
        return $this->organization_id;
    }

    /**
     * Set the value of Organization Id
     *
     * @param mixed organization_id
     *
     * @return self
     */
    public function setOrganizationId($organization_id)
    {
        $this->organization_id = $organization_id;

        return $this;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function getLinkedinId()
    {
        return $this->linkedin_id;
    }
    
    public function setLinkedinId($linkedin_id)
    {
        $this->linkedin_id = $linkedin_id;
        
        return $this;
    }
    
    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'token' => $this->token,
            'created_date' => $this->created_date,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'nickname' => $this->nickname,
            'suspension_date' => $this->suspension_date,
            'suspension_reason' => $this->suspension_reason,
            'organization_id' => $this->organization_id,
            'email' => $this->email,
            'avatar' => $this->avatar,
            'expiration_date' => $this->expiration_date,
            'has_linkedin' => ($this->linkedin_id !== null),
        ];
    }
}

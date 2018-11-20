<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class User extends AbstractModel
{
 	protected $id;
	protected $firstname;
	protected $lastname;
	protected $nickname;
	protected $status;
	protected $email;
	protected $password;
	protected $new_password;
	protected $birth_date;
	protected $position;
	protected $organization_id;
	protected $interest;
	protected $gender;
	protected $nationality;
	protected $origin;
	protected $avatar;
	protected $has_email_notifier;
	protected $deleted_date;
	protected $background;
	protected $timezone;
	protected $sis;
	protected $ambassador;
	protected $created_date;
	protected $suspension_date;
	protected $suspension_reason;
	protected $email_sent;
	protected $address_id;
	protected $linkedin_id;
	protected $is_active;
	protected $welcome_date;
	protected $welcome_delay;
	protected $cgu_accepted;
	protected $swap_email;
	protected $swap_token;
	protected $initial_email;
	protected $invitation_date;
	protected $description;
	protected $graduation_year;
	protected $linkedin_url;
	protected $has_email_contact_request_notifier;
	protected $sso_uid;

	protected $prefix = 'user';

	public function getId()
	{
		return $this->id;
	}

	public function setId($id)
	{
		$this->id = $id;

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

	public function getNickname()
	{
		return $this->nickname;
	}

	public function setNickname($nickname)
	{
		$this->nickname = $nickname;

		return $this;
	}

	public function getStatus()
	{
		return $this->status;
	}

	public function setStatus($status)
	{
		$this->status = $status;

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

	public function getPassword()
	{
		return $this->password;
	}

	public function setPassword($password)
	{
		$this->password = $password;

		return $this;
	}

	public function getNewPassword()
	{
		return $this->new_password;
	}

	public function setNewPassword($new_password)
	{
		$this->new_password = $new_password;

		return $this;
	}

	public function getBirthDate()
	{
		return $this->birth_date;
	}

	public function setBirthDate($birth_date)
	{
		$this->birth_date = $birth_date;

		return $this;
	}

	public function getPosition()
	{
		return $this->position;
	}

	public function setPosition($position)
	{
		$this->position = $position;

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

	public function getInterest()
	{
		return $this->interest;
	}

	public function setInterest($interest)
	{
		$this->interest = $interest;

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

	public function getNationality()
	{
		return $this->nationality;
	}

	public function setNationality($nationality)
	{
		$this->nationality = $nationality;

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

	public function getAvatar()
	{
		return $this->avatar;
	}

	public function setAvatar($avatar)
	{
		$this->avatar = $avatar;

		return $this;
	}

	public function getHasEmailNotifier()
	{
		return $this->has_email_notifier;
	}

	public function setHasEmailNotifier($has_email_notifier)
	{
		$this->has_email_notifier = $has_email_notifier;

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

	public function getBackground()
	{
		return $this->background;
	}

	public function setBackground($background)
	{
		$this->background = $background;

		return $this;
	}

	public function getTimezone()
	{
		return $this->timezone;
	}

	public function setTimezone($timezone)
	{
		$this->timezone = $timezone;

		return $this;
	}

	public function getSis()
	{
		return $this->sis;
	}

	public function setSis($sis)
	{
		$this->sis = $sis;

		return $this;
	}

	public function getAmbassador()
	{
		return $this->ambassador;
	}

	public function setAmbassador($ambassador)
	{
		$this->ambassador = $ambassador;

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

	public function getEmailSent()
	{
		return $this->email_sent;
	}

	public function setEmailSent($email_sent)
	{
		$this->email_sent = $email_sent;

		return $this;
	}

	public function getAddressId()
	{
		return $this->address_id;
	}

	public function setAddressId($address_id)
	{
		$this->address_id = $address_id;

		return $this;
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

	public function getIsActive()
	{
		return $this->is_active;
	}

	public function setIsActive($is_active)
	{
		$this->is_active = $is_active;

		return $this;
	}

	public function getWelcomeDate()
	{
		return $this->welcome_date;
	}

	public function setWelcomeDate($welcome_date)
	{
		$this->welcome_date = $welcome_date;

		return $this;
	}

	public function getWelcomeDelay()
	{
		return $this->welcome_delay;
	}

	public function setWelcomeDelay($welcome_delay)
	{
		$this->welcome_delay = $welcome_delay;

		return $this;
	}

	public function getCguAccepted()
	{
		return $this->cgu_accepted;
	}

	public function setCguAccepted($cgu_accepted)
	{
		$this->cgu_accepted = $cgu_accepted;

		return $this;
	}

	public function getSwapEmail()
	{
		return $this->swap_email;
	}

	public function setSwapEmail($swap_email)
	{
		$this->swap_email = $swap_email;

		return $this;
	}

	public function getSwapToken()
	{
		return $this->swap_token;
	}

	public function setSwapToken($swap_token)
	{
		$this->swap_token = $swap_token;

		return $this;
	}

	public function getInitialEmail()
	{
		return $this->initial_email;
	}

	public function setInitialEmail($initial_email)
	{
		$this->initial_email = $initial_email;

		return $this;
	}

	public function getInvitationDate()
	{
		return $this->invitation_date;
	}

	public function setInvitationDate($invitation_date)
	{
		$this->invitation_date = $invitation_date;

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

	public function getGraduationYear()
	{
		return $this->graduation_year;
	}

	public function setGraduationYear($graduation_year)
	{
		$this->graduation_year = $graduation_year;

		return $this;
	}

	public function getLinkedinUrl()
	{
		return $this->linkedin_url;
	}

	public function setLinkedinUrl($linkedin_url)
	{
		$this->linkedin_url = $linkedin_url;

		return $this;
	}

	public function getHasEmailContactRequestNotifier()
	{
		return $this->has_email_contact_request_notifier;
	}

	public function setHasEmailContactRequestNotifier($has_email_contact_request_notifier)
	{
		$this->has_email_contact_request_notifier = $has_email_contact_request_notifier;

		return $this;
	}

	public function getSsoUid()
	{
		return $this->sso_uid;
	}

	public function setSsoUid($sso_uid)
	{
		$this->sso_uid = $sso_uid;

		return $this;
	}

}
<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class Session extends AbstractModel
{
 	protected $token;
	protected $data;
	protected $uid;
	protected $registration_id;
	protected $uuid;
	protected $package;

	protected $prefix = 'session';

	public function getToken()
	{
		return $this->token;
	}

	public function setToken($token)
	{
		$this->token = $token;

		return $this;
	}

	public function getData()
	{
		return $this->data;
	}

	public function setData($data)
	{
		$this->data = $data;

		return $this;
	}

	public function getUid()
	{
		return $this->uid;
	}

	public function setUid($uid)
	{
		$this->uid = $uid;

		return $this;
	}

	public function getRegistrationId()
	{
		return $this->registration_id;
	}

	public function setRegistrationId($registration_id)
	{
		$this->registration_id = $registration_id;

		return $this;
	}

	public function getUuid()
	{
		return $this->uuid;
	}

	public function setUuid($uuid)
	{
		$this->uuid = $uuid;

		return $this;
	}

	public function getPackage()
	{
		return $this->package;
	}

	public function setPackage($package)
	{
		$this->package = $package;

		return $this;
	}

}
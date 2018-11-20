<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class EventUser extends AbstractModel
{
 	protected $event_id;
	protected $user_id;
	protected $read_date;
	protected $view_date;

	protected $prefix = 'event_user';

	public function getEventId()
	{
		return $this->event_id;
	}

	public function setEventId($event_id)
	{
		$this->event_id = $event_id;

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

	public function getReadDate()
	{
		return $this->read_date;
	}

	public function setReadDate($read_date)
	{
		$this->read_date = $read_date;

		return $this;
	}

	public function getViewDate()
	{
		return $this->view_date;
	}

	public function setViewDate($view_date)
	{
		$this->view_date = $view_date;

		return $this;
	}

}
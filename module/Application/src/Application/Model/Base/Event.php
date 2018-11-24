<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class Event extends AbstractModel
{
 	protected $id;
	protected $user_id;
	protected $source;
	protected $date;
	protected $event;
	protected $object;
	protected $target;
	protected $text;
	protected $picture;
	protected $target_id;
	protected $important;

	protected $prefix = 'event';

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

	public function getSource()
	{
		return $this->source;
	}

	public function setSource($source)
	{
		$this->source = $source;

		return $this;
	}

	public function getDate()
	{
		return $this->date;
	}

	public function setDate($date)
	{
		$this->date = $date;

		return $this;
	}

	public function getEvent()
	{
		return $this->event;
	}

	public function setEvent($event)
	{
		$this->event = $event;

		return $this;
	}

	public function getObject()
	{
		return $this->object;
	}

	public function setObject($object)
	{
		$this->object = $object;

		return $this;
	}

	public function getTarget()
	{
		return $this->target;
	}

	public function setTarget($target)
	{
		$this->target = $target;

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

	public function getPicture()
	{
		return $this->picture;
	}

	public function setPicture($picture)
	{
		$this->picture = $picture;

		return $this;
	}

	public function getTargetId()
	{
		return $this->target_id;
	}

	public function setTargetId($target_id)
	{
		$this->target_id = $target_id;

		return $this;
	}

	public function getImportant()
	{
		return $this->important;
	}

	public function setImportant($important)
	{
		$this->important = $important;

		return $this;
	}

}
<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class PageProgram extends AbstractModel
{
 	protected $id;
	protected $name;
	protected $user_id;
	protected $page_id;
	protected $created_date;

	protected $prefix = 'page_program';

	public function getId()
	{
		return $this->id;
	}

	public function setId($id)
	{
		$this->id = $id;

		return $this;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setName($name)
	{
		$this->name = $name;

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

	public function getPageId()
	{
		return $this->page_id;
	}

	public function setPageId($page_id)
	{
		$this->page_id = $page_id;

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

}
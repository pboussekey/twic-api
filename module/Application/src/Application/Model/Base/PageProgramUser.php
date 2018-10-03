<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class PageProgramUser extends AbstractModel
{
 	protected $user_id;
	protected $page_program_id;
	protected $created_date;

	protected $prefix = 'page_program_user';

	public function getUserId()
	{
		return $this->user_id;
	}

	public function setUserId($user_id)
	{
		$this->user_id = $user_id;

		return $this;
	}

	public function getPageProgramId()
	{
		return $this->page_program_id;
	}

	public function setPageProgramId($page_program_id)
	{
		$this->page_program_id = $page_program_id;

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
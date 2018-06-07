<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class UserTag extends AbstractModel
{
 	protected $user_id;
	protected $tag_id;

	protected $prefix = 'user_tag';

	public function getUserId()
	{
		return $this->user_id;
	}

	public function setUserId($user_id)
	{
		$this->user_id = $user_id;

		return $this;
	}

	public function getTagId()
	{
		return $this->tag_id;
	}

	public function setTagId($tag_id)
	{
		$this->tag_id = $tag_id;

		return $this;
	}

}
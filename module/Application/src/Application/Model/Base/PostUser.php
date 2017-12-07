<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class PostUser extends AbstractModel
{
 	protected $post_id;
	protected $user_id;
	protected $hidden;

	protected $prefix = 'post_user';

	public function getPostId()
	{
		return $this->post_id;
	}

	public function setPostId($post_id)
	{
		$this->post_id = $post_id;

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

	public function getHidden()
	{
		return $this->hidden;
	}

	public function setHidden($hidden)
	{
		$this->hidden = $hidden;

		return $this;
	}

}
<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class Hashtag extends AbstractModel
{
 	protected $name;
	protected $post_id;
	protected $type;
	protected $created_date;
	protected $user_id;
	protected $tag_id;

	protected $prefix = 'hashtag';

	public function getName()
	{
		return $this->name;
	}

	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	public function getPostId()
	{
		return $this->post_id;
	}

	public function setPostId($post_id)
	{
		$this->post_id = $post_id;

		return $this;
	}

	public function getType()
	{
		return $this->type;
	}

	public function setType($type)
	{
		$this->type = $type;

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
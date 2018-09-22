<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class TagBreakdown extends AbstractModel
{
 	protected $tag_id;
	protected $tag_part;

	protected $prefix = 'tag_breakdown';

	public function getTagId()
	{
		return $this->tag_id;
	}

	public function setTagId($tag_id)
	{
		$this->tag_id = $tag_id;

		return $this;
	}

	public function getTagPart()
	{
		return $this->tag_part;
	}

	public function setTagPart($tag_part)
	{
		$this->tag_part = $tag_part;

		return $this;
	}

}
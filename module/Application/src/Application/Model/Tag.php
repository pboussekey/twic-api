<?php

namespace Application\Model;

use Application\Model\Base\Tag as BaseTag;

class Tag extends BaseTag
{
  const SKILL='skill';
  const CAREER='career';
  const HOBBY='hobby';
  const LANGUAGE='language';
  const GRADUATION='graduation';
  const OTHER='other';

  protected $category;

  public function getCategory()
  {
      return $this->category;
  }

  public function setCategory($category)
  {
      $this->category = $category;

      return $this;
  }
}

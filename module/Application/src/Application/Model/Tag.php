<?php

namespace Application\Model;

use Application\Model\Base\Tag as BaseTag;

class Tag extends BaseTag
{
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

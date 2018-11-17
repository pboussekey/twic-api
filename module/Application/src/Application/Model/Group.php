<?php

namespace Application\Model;

use Application\Model\Base\Group as BaseGroup;

class Group extends BaseGroup
{

      protected $page_id;
      public function setPageId($page_id)
      {
          $this->page_id = $page_id;

          return $this;
      }

      public function getPageId()
      {
          return $this->page_id;
      }


}

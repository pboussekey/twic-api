<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class PageTag extends AbstractModel
{
    protected $page_id;
    protected $tag_id;

    protected $prefix = 'page_tag';

    public function getPageId()
    {
        return $this->page_id;
    }

    public function setPageId($page_id)
    {
        $this->page_id = $page_id;

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

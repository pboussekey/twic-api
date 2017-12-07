<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class PageDoc extends AbstractModel
{
    protected $page_id;
    protected $library_id;

    protected $prefix = 'page_doc';

    public function getPageId()
    {
        return $this->page_id;
    }

    public function setPageId($page_id)
    {
        $this->page_id = $page_id;

        return $this;
    }

    public function getLibraryId()
    {
        return $this->library_id;
    }

    public function setLibraryId($library_id)
    {
        $this->library_id = $library_id;

        return $this;
    }
}

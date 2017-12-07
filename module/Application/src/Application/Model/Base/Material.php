<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class Material extends AbstractModel
{
    protected $library_id;
    protected $course_id;

    protected $prefix = 'material';

    public function getLibraryId()
    {
        return $this->library_id;
    }

    public function setLibraryId($library_id)
    {
        $this->library_id = $library_id;

        return $this;
    }

    public function getCourseId()
    {
        return $this->course_id;
    }

    public function setCourseId($course_id)
    {
        $this->course_id = $course_id;

        return $this;
    }
}

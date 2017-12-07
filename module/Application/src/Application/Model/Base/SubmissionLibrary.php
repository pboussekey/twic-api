<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class SubmissionLibrary extends AbstractModel
{
    protected $submission_id;
    protected $library_id;

    protected $prefix = 'submission_library';

    public function getSubmissionId()
    {
        return $this->submission_id;
    }

    public function setSubmissionId($submission_id)
    {
        $this->submission_id = $submission_id;

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

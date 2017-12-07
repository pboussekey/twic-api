<?php

namespace Application\Service;

use Dal\Service\AbstractService;

class SubmissionLibrary extends AbstractService
{
    public function add($submission_id, $library_id)
    {
        $m_submission_library = $this->getModel()
          ->setSubmissionId($submission_id)
          ->setLibraryId($library_id);

        return $this->getMapper()->insert($m_submission_library);
    }

    public function getList($submission_id = null, $library_id = null)
    {
        $m_submission_library = $this->getModel()
          ->setSubmissionId($submission_id)
          ->setLibraryId($library_id);
    
        return $this->getMapper()->select($m_submission_library);
    }

    public function remove($submission_id, $library_id)
    {
        $m_submission_library = $this->getModel()
          ->setSubmissionId($submission_id)
          ->setLibraryId($library_id);

        return $this->getMapper()->delete($m_submission_library);
    }
}

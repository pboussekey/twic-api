<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class SubWhiteboard extends AbstractModel
{
    protected $submission_id;
    protected $whiteboard_id;

    protected $prefix = 'sub_whiteboard';

    public function getSubmissionId()
    {
        return $this->submission_id;
    }

    public function setSubmissionId($submission_id)
    {
        $this->submission_id = $submission_id;

        return $this;
    }

    public function getWhiteboardId()
    {
        return $this->whiteboard_id;
    }

    public function setWhiteboardId($whiteboard_id)
    {
        $this->whiteboard_id = $whiteboard_id;

        return $this;
    }
}

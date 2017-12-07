<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class SubTextEditor extends AbstractModel
{
    protected $submission_id;
    protected $text_editor_id;

    protected $prefix = 'sub_text_editor';

    public function getSubmissionId()
    {
        return $this->submission_id;
    }

    public function setSubmissionId($submission_id)
    {
        $this->submission_id = $submission_id;

        return $this;
    }

    public function getTextEditorId()
    {
        return $this->text_editor_id;
    }

    public function setTextEditorId($text_editor_id)
    {
        $this->text_editor_id = $text_editor_id;

        return $this;
    }
}

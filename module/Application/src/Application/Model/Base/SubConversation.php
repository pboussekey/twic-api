<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class SubConversation extends AbstractModel
{
    protected $conversation_id;
    protected $submission_id;

    protected $prefix = 'sub_conversation';

    public function getConversationId()
    {
        return $this->conversation_id;
    }

    public function setConversationId($conversation_id)
    {
        $this->conversation_id = $conversation_id;

        return $this;
    }

    public function getSubmissionId()
    {
        return $this->submission_id;
    }

    public function setSubmissionId($submission_id)
    {
        $this->submission_id = $submission_id;

        return $this;
    }
}

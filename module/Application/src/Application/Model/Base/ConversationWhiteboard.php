<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class ConversationWhiteboard extends AbstractModel
{
    protected $conversation_id;
    protected $whiteboard_id;

    protected $prefix = 'conversation_whiteboard';

    public function getConversationId()
    {
        return $this->conversation_id;
    }

    public function setConversationId($conversation_id)
    {
        $this->conversation_id = $conversation_id;

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

<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class ConversationTextEditor extends AbstractModel
{
    protected $conversation_id;
    protected $text_editor_id;

    protected $prefix = 'conversation_text_editor';

    public function getConversationId()
    {
        return $this->conversation_id;
    }

    public function setConversationId($conversation_id)
    {
        $this->conversation_id = $conversation_id;

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

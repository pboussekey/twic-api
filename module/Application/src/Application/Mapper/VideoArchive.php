<?php

namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;

class VideoArchive extends AbstractMapper
{
    /**
     * @param int $conversation_id
     *
     * @return \Dal\Db\ResultSet\ResultSet
     */
    public function getLastArchiveId($conversation_id)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(array('id', 'archive_token', 'archive_link', 'archive_status', 'archive_duration', 'conversation_id', 'created_date'))
          ->where(['video_archive.conversation_id' => $conversation_id])
          ->order('video_archive.created_date DESC')
          ->limit(1);

        return $this->selectWith($select);
    }
}

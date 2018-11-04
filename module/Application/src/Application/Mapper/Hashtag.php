<?php

namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;

class Hashtag extends AbstractMapper
{
      public function keepMentions($id, $user_id)
      {
          $delete = $this->tableGateway->getSql()->delete();
          $delete->where(['post_id' => $id, 'type' => '@']);
          $delete->where->notIn('user_id', $user_id);
          return $this->deleteWith($delete);
      }

      public function keepHashtags($id, $hashtags)
      {
          $delete = $this->tableGateway->getSql()->delete();
          $delete->where(['post_id' => $id, 'type' => '#']);
          $delete->where->notIn('name', $hashtags);
          return $this->deleteWith($delete);
      }

}

<?php

namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;

class Hashtag extends AbstractMapper
{
      public function keepMentions($id, $user_id)
      {
          $delete = $this->tableGateway->getSql()->delete();
          $delete->where(['post_id' => $id]);
          $delete->where->notIn('user_id', $user_id);
          return $this->deleteWith($delete);
      }

      public function keepMentions($id, $hashtags)
      {
          $delete = $this->tableGateway->getSql()->delete();
          $delete->where(['post_id' => $id]);
          $delete->where->notIn('name', $hashtags);
          return $this->deleteWith($delete);
      }

}

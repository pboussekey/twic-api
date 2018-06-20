<?php

namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;

class PostUser extends AbstractMapper
{

    public function show($id = null, $uid = null){
      $update = $this->tableGateway->getSql()->update();
      $update->set(['hidden' => 0]);
      if(null !== $id){
          $update->where(['post_id' => $id]);
      }
      if(null !== $uid){
        $update->join('post', 'post_id = post.id')
               ->where(['post.uid' => $uid]);
      }
      return $this->updateWith($update);
    }
}

<?php

namespace Application\Mapper;

use Dal\Mapper\AbstractMapper;
use Zend\Db\Sql\Predicate\Expression;

class Group extends AbstractMapper
{
  public function getListByPage($page_id)
  {
      $select = $this->tableGateway->getSql()->select();
      $select->columns([
          'id',
          'name',
          'item_id'
        ])
        ->join('item_user', new Expression('item_user.group_id = group.id AND item_user.deleted_date IS NULL'), [])
        ->join('item', 'item.id = item_user.item_id', ['group$page_id' => 'page_id'])
        ->where(['item.page_id ' => $page_id]);
      return $this->selectWith($select);
  }
}

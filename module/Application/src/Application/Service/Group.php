<?php

namespace Application\Service;

use Dal\Service\AbstractService;

class Group extends AbstractService
{
    /**
     * Get List
     *
     * @invokable
     *
     * @param int $item_id
     */
    public function getList($item_id)
    {
        if (!is_array($item_id)) {
            $item_id = [$item_id];
        }
        
        $ret = [];
        foreach ($item_id as $itm) {
            $ret[$itm] = [];
        }
        // indexer par item_id
        $m_group = $this->getModel()->setItemId($item_id);
        $res_group = $this->getMapper()->select($m_group);
        
        
        foreach ($res_group as $m_group) {
            $ret[$m_group->getItemId()][] = $m_group->toArray();
        }
        
        return $ret;
    }
    
    /**
     * Add GRoup
     *
     * @invokable
     *
     * @param int $name
     * @param int $item_id
     *
     * @return int
     */
    public function add($name, $item_id)
    {
        if (!is_array($name)) {
            $name = [$name];
        }
        
        $ret = [];
        foreach ($name as $n) {
            $m_group = $this->getModel()->setName($n)->setItemId($item_id);
            $this->getMapper()->insert($m_group);
            $ret[] = $this->getMapper()->getLastInsertValue();
        }
        
        return $ret;
    }
    
    /**
     * Delete Group
     *
     * @invokable
     *
     * @param int $id
     */
    public function delete($id)
    {
        $m_group = $this->getModel()->setId($id);
        
        return $this->getMapper()->delete($m_group);
    }
    
    /**
     * Get Or Create GRoup
     *
     * @param string $group_name
     * @param int $item_id
     *
     * @return \Application\Model\Group
     */
    public function getOrCreate($name, $item_id)
    {
        $m_group = $this->getModel()->setName($name)->setItemId($item_id);
        
        $res_group = $this->getMapper()->select($m_group);
        if ($res_group->count() <= 0) {
            $this->getMapper()->insert($m_group);
            $m_group->setId($this->getMapper()->getLastInsertValue());
        } else {
            $m_group = $res_group->current();
        }
        
        return $m_group;
    }
}

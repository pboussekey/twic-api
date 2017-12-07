<?php
namespace Application\Service;

use Dal\Service\AbstractService;
use Application\Model\PageRelation as ModelPageRelation;

class PageRelation extends AbstractService
{

    /**
     * Add relation type
     *
     * @param int $page_id
     * @param int $parent_id
     * @param string $type
     *
     * @return int
     */
    public function add($page_id, $parent_id, $type)
    {
        $m_page_relation = $this->getModel()
            ->setParentId($parent_id)
            ->setPageId($page_id)
            ->setType($type);
        
        return $this->getMapper()->insert($m_page_relation);
    }

    /**
     * GetList relation By Type
     *
     * @param int $page_id
     * @param string $type
     *
     * @return \Dal\Db\ResultSet\ResultSet
     */
    public function getList($page_id, $type = null)
    {
        return $this->getMapper()->select($this->getModel()
            ->setPageId($page_id)
            ->setType($type));
    }

    /**
     * GetList relation Owner
     *
     * @param int $page_id
     *
     * @return \Dal\Db\ResultSet\ResultSet
     */
    public function getOwner($page_id)
    {
        return $this->getList($page_id, ModelPageRelation::TYPE_OWNER);
    }
}

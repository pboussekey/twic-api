<?php

namespace Application\Service;

use Dal\Service\AbstractService;
use PharIo\Manifest\Application;

class PageProgram extends AbstractService
{
    /**
     * Add program to a school organization
     * 
     * @invokable
     * 
     * @param int $page_id
     * @param string $name
     * 
     * @return int
     */
    public function add($page_id, $name)
    {
        $user_id = $this->getServiceUser()->getIdentity()['id'];
        $m_page_program = $this->getModel()
            ->setPageId($page_id)
            ->setUserId($user_id)
            ->setName($name)
            ->setCreatedDate((new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s'));
        
        $ret = 0;
        if($this->getMapper()->insert($m_page_program)) {
            $ret = $this->getMapper()->getLastInsertValue();
        }
        
        return $ret;
    }
    
    /**
     * Get list program by page id
     * 
     * @invokable
     * 
     * @param int $page_id
     * 
     * @return \Dal\Db\ResultSet\ResultSet
     */
    public function getList($page_id)
    {
        return $this->getMapper()->getList($page_id);
    }
    
    /**
     * Get Lite Page Program
     * 
     * @param int $id
     * 
     * @return \Application\Model\PageProgram
     */
    public function get($id)
    {
        $m_prage_program = $this->getModel()->setId($id);
        return $this->getMapper()->select($m_prage_program)->current();
    }
    
    /**
     * delete Page Program
     *
     * @invokable
     * 
     * @param int $id
     *
     * @return \Application\Model\PageProgram
     */
    public function delete($id)
    {
        $m_page_program = $this->get($id);
        
        if(!$this->getServicePage()->isAdmin($m_page_program->getPageId())) {
            throw new \Exception("No authorization to delete program");
        }
        
        $m_prage_program = $this->getModel()->setId($id);
        
        return $this->getMapper()->delete($m_prage_program);
    }
    
    /**
     * Get Service User
     *
     * @return \Application\Service\User
     */
    private function getServiceUser()
    {
        return $this->container->get('app_service_user');
    }
    
    /**
     * Get Service Page
     *
     * @return \Application\Service\Page
     */
    private function getServicePage()
    {
        return $this->container->get('app_service_page');
    }
    
}
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
        $ret = 0;
        $user_id = $this->getServiceUser()->getIdentity()['id'];
        
        $m_page_program = $this->getModel()
            ->setPageId($page_id)
            ->setName($name);
        
        $res_page_program = $this->getMapper()->select($m_page_program);
        if($res_page_program->count() < 1) {
            $m_page_program = $this->getModel()
                ->setPageId($page_id)
                ->setUserId($user_id)
                ->setName($name)
                ->setCreatedDate((new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s'));
            
            
            if($this->getMapper()->insert($m_page_program)) {
                $ret = $this->getMapper()->getLastInsertValue();
            }
        } else {
            $ret = $res_page_program->current()->getId();
        }
        
        return $ret;
    }
    
    /**
     * Get list program by page id
     * 
     * @invokable
     * 
     * @param int $page_id
     * @param string $search
     * @param array $filter
     * 
     * @return \Dal\Db\ResultSet\ResultSet
     */
    public function getList($page_id, $search = null, $filter = null)
    {
        $mapper = $this->getMapper();
        $res_page_program = $mapper->usePaginator($filter)->getList($page_id, $search);
        
        return [ 'list' => $res_page_program, 'count' => $mapper->count() ];
    }
    
    /**
     * Get list program by user_id
     * 
     * @param int $user_id
     * 
     * @return \Dal\Db\ResultSet\ResultSet
     */
    public function getListUserId($user_id)
    {
        return $this->getMapper()->getList(null, null, $user_id);
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
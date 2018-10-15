<?php

namespace Application\Service;

use Dal\Service\AbstractService;

class PageProgramUser extends AbstractService
{
    /**
     * Add user to a program
     * 
     * @invokable
     * 
     * @param int $page_program_id
     * @param int $user_id
     * 
     * @return int
     */
    public function add($page_program_id = null, $user_id =  null, $page_program_name = null)
    {
        if(null === $page_program_id && null === $page_program_name) {
            new \Exception("Erro params");
        }
        
        if(null === $user_id) {
            $user_id = $this->getServiceUser()->getIdentity()['id'];
        }
        
        if(null === $page_program_id && null !== $page_program_name) {
            $m_user = $this->getServiceUser()->getLite($user_id);
            $page_program_id = $this->getServicePageProgram()->add($m_user->getOrganizationId(), $page_program_name);
        }
        
        $this->deleteAll($user_id);
        $m_page_program_user = $this->getModel()
            ->setPageProgramId($page_program_id)
            ->setUserId($user_id)
            ->setCreatedDate((new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s'));
        
         return $this->getMapper()->insert($m_page_program_user);
    }

    /**
     * Delete user in to program
     * 
     * @invokable
     * 
     * @param int $page_program_id
     * @return boolean
     */
    public function delete($page_program_id, $user_id = null)
    {
        $m_page_program = $this->getServicePageProgram()->get($page_program_id);
        
        if(null === $user_id || !$this->getServicePage()->isAdmin($m_page_program->getPageId())) {
            $user_id = $this->getServiceUser()->getIdentity()['id'];
        }
        
        $m_page_program = $this->getModel()
            ->setPageProgramId($page_program_id)
            ->setUserId($user_id);
        
        return $this->getMapper()->delete($m_page_program);
    }
    
    /**
     * Delete user in to program
     *
     * @param int $user_id
     * 
     * @return boolean
     */
    public function deleteAll($user_id)
    {
        $m_page_program = $this->getModel()->setUserId($user_id);
        
        return $this->getMapper()->delete($m_page_program);
    }
    
    /**
     * Get List programme user
     * 
     * @param int $user_id
     */
    public function getList($user_id)
    {
        $m_page_program_user = $this->getModel()
            ->setUserId($user_id);
        
        return $this->getMapper()->select($m_page_program_user);
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
     * Get Service User
     *
     * @return \Application\Service\PageProgram
     */
    private function getServicePageProgram()
    {
        return $this->container->get('app_service_page_program');
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
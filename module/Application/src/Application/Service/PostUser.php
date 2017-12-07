<?php

namespace Application\Service;

use Dal\Service\AbstractService;

class PostUser extends AbstractService
{
    
     /**
     * Hide post
     *
     * @param int   $id
     */
    public function hide($id)
    {
        $identity = $this->getServiceUser()->getIdentity();
        $m_post_user = $this->getModel()->setUserId($identity['id'])->setPostId($id)->setHidden(1);
        if($this->getMapper()->select($m_post_user)->count() === 0){
            $this->getMapper()->insert($m_post_user);
        }
        else{
            $this->getMapper()->update($m_post_user);
        }
        
        return true;
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
}
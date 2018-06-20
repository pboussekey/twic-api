<?php

namespace Application\Service;

use Dal\Service\AbstractService;

class PostUser extends AbstractService
{

     /**
      * Hide post
      *
      * @param int $id
      */
    public function hide($id)
    {
        $identity = $this->getServiceUser()->getIdentity();
        $m_post_user = $this->getModel()->setUserId($identity['id'])->setPostId($id);
        $count = $this->getMapper()->select($m_post_user)->count();
        $m_post_user->setHidden(1);
        if($count === 0) {
            $this->getMapper()->insert($m_post_user);
        }
        else{
            $this->getMapper()->update($m_post_user);
        }

        return true;
    }


  /**
   * Force showing post
   *
   * @param int $id
   * @param string $uid
   */
   public function show($id = null, $uid = null)
   {
       if(null !== $id || null !== $uid){
          return $this->getMapper()->show($id, $uid);
       }
       else{
          return 0;
       }
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

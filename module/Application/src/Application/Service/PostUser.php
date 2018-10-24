<?php

namespace Application\Service;

use Dal\Service\AbstractService;
use Zend\Db\Sql\Predicate\IsNull;

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
      * Hide post
      *
      * @param int $id
      */
      public function view($id)
      {
          if(!is_array($id)){
              $id = [$id];
          }
          $identity = $this->getServiceUser()->getIdentity();
          $date = (new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s');
          foreach ($id as $pid) {
              $m_post_user = $this->getModel()->setUserId($identity['id'])->setPostId($pid);
              $res_post_user = $this->getMapper()->select($m_post_user);
              $m_post_user->setViewDate($date);
              $m_previous_post_user = $res_post_user->current();
              if($m_previous_post_user === false) {
                  $this->getMapper()->insert($m_post_user);
              }
              else if($m_previous_post_user->getViewDate() instanceof isNull){
                  $this->getMapper()->update($m_post_user);
              }
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

<?php

namespace Application\Service;

use Dal\Service\AbstractService;
use Zend\Db\Sql\Predicate\IsNull;

class EventUser extends AbstractService
{


    /**
     * Get events list for current user.
     *
     * @param array|int $event_id
     * @param array|int $user_id
     *
     *
     * @return array
     */
    public function add($event_id, $user_id){

          if(!is_array($user_id)){
              $user_id = [$user_id];
          }
          $last = [];
          $previous = $this->getServiceEvent()->getLast($event_id, $user_id);
          $ret = 0;
          foreach($user_id as $uid){
              $m_event_user = $this->getModel()
                  ->setUserId($uid)
                  ->setEventId($event_id);
              if(isset($previous[$uid])){
                  $m_event_user->setPreviousId($previous[$uid]);
              }
              $ret += $this->getMapper()->insert($m_event_user);
          }

          return $ret;

    }

    /**
     * Get events list for current user.
     *
     * @param array|int $event_id
     *
     *
     * @return array
     */
    public function read($event_id){
        $user_id = $this->getServiceUser()->getIdentity()['id'];
        $ret = 0;
        if(null !== $event_id){
            foreach($event_id as $eid){
                $m_event_user = $this->getModel()
                    ->setUserId($user_id)
                    ->setEventId($eid);
                $res_event_user = $this->getMapper()->select($m_event_user);
                if($res_event_user->count() === 0){
                    $this->getMapper()->insert($m_event_user->setReadDate((new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s')));
                    $ret++;
                }
                else if($res_event_user->current()->getReadDate() instanceof IsNull){
                    $this->getMapper()->update($m_event_user->setReadDate((new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s')));
                    $ret++;
                }
            }
        }
        else{
            return $this->getMapper()->update($this->getModel()->setReadDate((new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s')),
                  ['user_id' => $user_id, new IsNull('read_date')]);
        }


        return $ret;
    }

    /**
     * Get Service User.
     *
     * @return \Application\Service\User
     */
    private function getServiceUser()
    {
        return $this->container->get('app_service_user');
    }

    /**
     * Get Service Event.
     *
     * @return \Application\Service\Event
     */
    private function getServiceEvent()
    {
        return $this->container->get('app_service_event');
    }
}

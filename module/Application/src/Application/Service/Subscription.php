<?php

namespace Application\Service;

use Dal\Service\AbstractService;

class Subscription extends AbstractService
{
    public function add($libelle, $user_id = null)
    {
        $ret = null;
        if (null === $user_id) {
            $user_id = $this->getServiceUser()->getIdentity()['id'];
        }

        $m_subscription = $this->getModel()
            ->setLibelle($libelle)
            ->setUserId($user_id);

        $res_subscription = $this->getMapper()->select($m_subscription);

        if ($res_subscription->count() <= 0) {
            $ret = $this->getMapper()->insert($m_subscription->setCreatedDate((new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s')));
        }
        
        return $ret;
    }

    /**
     * Get List User Id
     *
     * @param  string $libelle
     * @param array $filter 
     * @param  string $search
     * @return array
     */
    public function getListUserId($libelle, $filter = null, $search = null)
    {
        if (!is_array($libelle)) {
            $libelle = [$libelle];
        }
        $u = [];
        foreach ($libelle as $l) {
            if (strpos($l, 'M') === 0) {
                $u[] = (int)substr($l, 1);
            } else {
                if(null != $filter){
                    $res_subscription = $this->getMapper()->usePaginator($filter)->select($this->getModel()->setLibelle($l));
                }
                else{
                    $res_subscription = $this->getMapper()->select($this->getModel()->setLibelle($l));
                }
                foreach ($res_subscription as $m_subscription) {
                    $u[] = $m_subscription->getUserId();
                }
            }
        }

        return null != $filter ? [ 'list' => array_unique($u), 'count' => $this->getMapper()->count() ] : array_unique($u);
    }

    public function delete($libelle, $user_id = null)
    {
        if (null === $user_id) {
            $user_id = $this->getServiceUser()->getIdentity()['id'];
        }

        $m_subscription = $this->getModel()
            ->setLibelle($libelle)
            ->setUserId($user_id);

        return $this->getMapper()->delete($m_subscription);
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
     * Get Service PageRelation
     *
     * @return \Application\Service\PageRelation
     */
    private function getServicePageRelation()
    {
        return $this->container->get('app_service_page_relation');
    }
}

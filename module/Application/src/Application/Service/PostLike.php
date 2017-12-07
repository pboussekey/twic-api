<?php

namespace Application\Service;

use Dal\Service\AbstractService;
use Application\Model\Page as ModelPage;
use Application\Model\PostSubscription as ModelPostSubscription;

class PostLike extends AbstractService
{
    /**
     * Add Liek to Post
     *
     * @param  int $post_id
     * @param  int $type
     * @throws \Exception
     * @return int
     */
    public function add($post_id, $type = 1)
    {
        $res = null;
        $user_id = $this->getServiceUser()->getIdentity()['id'];
        $m_post_like = $this->getModel()
            ->setPostId($post_id)
            ->setUserId($user_id);

        $m = $this->getMapper()->select($m_post_like);

        if ($m && $m->count() > 0) {
            $res = $this->getMapper()->update(
                $this->getModel()->setIsLike(true),
                [
                  'post_id' => $post_id,
                  'user_id' => $user_id
                ]
            );
        } else {
            $date = (new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s');
            $m_post_like->setIsLike(true)->setCreatedDate($date);

            if ($this->getMapper()->insert($m_post_like) <= 0) {
                throw new \Exception('error add like');
            }
            $res = $this->getMapper()->getLastInsertValue();

            /*
             * Subscription
             */
            $m_post = $this->getServicePost()->getLite($post_id);
            $m_post_like = $this->getLite($res);
            $sub_post = [
                'P'.$this->getServicePost()->getTarget($m_post),
                'M'.$m_post->getUserId(),
            ];

            $origin_id = $m_post->getOriginId();
            $base_id = (is_numeric($origin_id)) ? $origin_id:$post_id;
            $m_post_base = $this->getServicePost()->getLite($origin_id);
            if (is_numeric($origin_id)) {
                $sub_post = array_merge($sub_post, ['P'.$this->getServicePost()->getTarget($m_post_base)]);
            }

            $is_private_page = ($m_post_base && is_numeric($m_post_base->getTPageId()) && ($this->getServicePage()->getLite($m_post_base->getTPageId())->getConfidentiality() === ModelPage::CONFIDENTIALITY_PRIVATE));
            // si ce n'est pas privé on notifie les personne abonné au propriétaitre du like et du poste
            if (!$is_private_page) {
                $sub_post = array_merge(
                    $sub_post,
                    [
                    'P'.$this->getServicePost()->getOwner($m_post),
                    'P'.$this->getUserLike($m_post_like),
                    ]
                );
            }

            $this->getServicePostSubscription()->add(
                array_unique($sub_post),
                $base_id,
                $date,
                ModelPostSubscription::ACTION_LIKE,
                $user_id,
                null,
                ['id' => $post_id, 'parent_id' => $m_post->getParentId(), 'origin_id' => $m_post->getOriginId()]
            );
        }

        return $res;
    }

    /**
     * UnLike Post
     *
     * @toto check que le user soit bien encore admin de la page ou de lorganization
     *
     * @param  int $post_id
     * @return int
     */
    public function delete($post_id)
    {
        return $this->getMapper()->update(
            $this->getModel()->setIsLike(false),
            [
            'post_id' => $post_id, 'user_id' => $this->getServiceUser()->getIdentity()['id']]
        );
    }

    /**
     * Get Post Like Lite
     *
     * @param  int $id
     * @return \Application\Model\PostLike
     */
    public function getLite($id)
    {
        $res_post_like = $this->getMapper()->select($this->getModel()->setId($id));

        return (is_array($id)) ?
            $res_post_like :
            $res_post_like->current();
    }

    public function getUserLike(\Application\Model\PostLike $m_post_like)
    {
        switch (true) {
          case (is_numeric($m_post_like->getPostId())):
              $u = 'P'.$m_post_like->getPostId();
              break;
          default:
              $u ='U'.$m_post_like->getUserId();
              break;
        }

        return $u;
    }
    
     /**
     * Get page counts.
     *
     * @invokable
     *
     * @param string  $start_date
     * @param string  $end_date
     * @param string  $interval_date
     * @param string  $type
     * @param int     $organization_id
     *
     * @return array
     */
    public function getCount( $start_date = null, $end_date = null, $interval_date = 'D', $type = null, $organization_id  = null){
        
        $interval = $this->getServiceActivity()->interval($interval_date);
        $identity = $this->getServiceUser()->getIdentity();
        
        return $this->getMapper()->getCount($identity['id'],$interval, $start_date, $end_date, $organization_id, $type);
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
     * Get Service Post Like
     *
     * @return \Application\Service\Post
     */
    private function getServicePost()
    {
        return $this->container->get('app_service_post');
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

    /**
     * Get Service Post Like
     *
     * @return \Application\Service\PostSubscription
     */
    private function getServicePostSubscription()
    {
        return $this->container->get('app_service_post_subscription');
    }

    /**
     * Get Service Activity
     *
     * @return \Application\Service\Activity
     */
    private function getServiceActivity()
    {
        return $this->container->get('app_service_activity');
    }      
}

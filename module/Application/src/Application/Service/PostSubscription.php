<?php
/**
 * TheStudnet (http://thestudnet.com)
 *
 * Post Subscription
 */
namespace Application\Service;

use Dal\Service\AbstractService;
use Application\Model\PostSubscription as ModelPostSubscription;

/**
 * Class PostSubscription
 */
class PostSubscription extends AbstractService
{
    /**
     * Add Post Subscription
     *
     * @param  string $libelle
     * @param  int    $post_id
     * @param  string $last_date
     * @param  string $action
     * @param  int    $user_id
     * @param  int    $sub_post_id
     * @param  mixed  $data
     * @return bool
     */
    public function add($libelle, $post_id, $last_date, $action, $user_id, $sub_post_id =null, $data = null)
    {
        if (!is_array($libelle)) {
            $libelle = [$libelle];
        }

        if (is_array($data)) {
            $data = json_encode($data);
        }

        $m_post_subscription = $this->getModel()
            ->setPostId($post_id)
            ->setAction($action)
            ->setUserId($user_id)
            ->setSubPostId($sub_post_id)
            ->setData($data)
            ->setLastDate($last_date);

        foreach ($libelle as $l) {
            $m_post_subscription->setLibelle($l);
            $this->getMapper()->insert($m_post_subscription);
        }

        $m_post = $this->getServicePost()->getLite($post_id);

        $this->getServiceEvent()->userPublication($libelle, ($sub_post_id !== null) ? $sub_post_id : $m_post->getId(), $m_post->getType(), $action);

        return true;
    }

    /**
     * Hard Delete Post Subscription
     *
     * @param  string $libelle
     * @param  int    $post_id
     * @return int
     */
    public function delete($libelle, $post_id)
    {
        $m_post_subscription = $this->getModel()
            ->setLibelle($libelle)
            ->setPostId($post_id);

        return $this->getMapper()->delete($m_post_subscription);
    }

    /**
    * Add Tag in Post
    *
    * @param array  $ar
    * @param int    $id
    * @param string $date
    **/
    public function addHashtag($ar, $id, $date)
    {
        $user_id = $this->getServiceUser()->getIdentity()['id'];
        foreach ($ar as $n) {
            if (substr($n, 0, 1) === '@') {
                $tab = json_decode(str_replace("'", "\"", substr($n, 1)), true);
                // remonte le post des abonner a la personne tagé
                $this->add('U'.$tab[0].$tab[1], $id, $date, ModelPostSubscription::ACTION_TAG, $user_id);
            }
        }
    }

    /**
     * Get Last Model Post Subscription
     *
     * @param int $post_id
     *
     * @return \Application\Model\PostSubscription
     */
    public function getLast($post_id)
    {
        $user_id = $this->getServiceUser()->getIdentity()['id'];

        $m_post_subscription = $this->getMapper()->getLast($post_id, $user_id)->current();

        if ($m_post_subscription && is_string($m_post_subscription->getData())) {
            $m_post_subscription->setData(json_decode($m_post_subscription->getData(), true));
        }

        return $m_post_subscription;
    }

    /**
     * Get List Libelle of Post
     *
     * @param int $post_id
     *
     * @return array
     */
    public function getListLibelle($post_id)
    {
        $res_post_subscription = $this->getMapper()->getListLibelle($post_id);

        $lib = [];
        foreach ($res_post_subscription as $m_post_subscription) {
            $lib[] = $m_post_subscription->getLibelle();
        }

        return array_unique($lib);
    }

    /**
     * Get Last Post Subscription
     *
     * @param int $post_id
     *
     * @return \Application\Model\PostSubscription
     */
    public function getLastLite($post_id)
    {
        $user_id = $this->getServiceUser()->getIdentity()['id'];

        $m_post_subscription = $this->getMapper()->getLastLite($post_id, $user_id)->current();

        if ($m_post_subscription && is_string($m_post_subscription->getData())) {
            $m_post_subscription->setData(json_decode($m_post_subscription->getData(), true));
        }

        return $m_post_subscription;
    }

    /**
     * Get Service Post
     *
     * @return \Application\Service\Post
     */
    private function getServicePost()
    {
        return $this->container->get('app_service_post');
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
     * Get Service Event.
     *
     * @return \Application\Service\Event
     */
    private function getServiceEvent()
    {
        return $this->container->get('app_service_event');
    }
}

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

    function limitText($text, $length = 50){
        return strlen($text) > $length ? substr($text, 0,  $length).'...' : $text;
    }


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
    public function add($libelle, $post_id, $last_date, $action, $user_id, $sub_post_id =null, $data = null, $is_not_public = false, $notify = null)
    {
        if (!is_array($libelle)) {
            $libelle = [$libelle];
        }

        $m_post_subscription = $this->getModel()
            ->setPostId($post_id)
            ->setAction($action)
            ->setUserId($user_id)
            ->setSubPostId($sub_post_id)
            ->setData(is_array($data) ? json_encode($data) : $data)
            ->setLastDate($last_date);
        foreach ($libelle as $l) {
            $m_post_subscription->setLibelle($l);
            $this->getMapper()->insert($m_post_subscription);
        }
        if($notify === false){
            return true;
        }
        $m_post = $this->getServicePost()->getLite($post_id);
        if($is_not_public){
            foreach($libelle as $key => $val){
                if(strcmp(substr($val, 0, 2), 'PU') === 0){
                    unset($libelle[$key]);
                }
            }
        }
        $post_id = null !== $sub_post_id ? $sub_post_id : $post_id;
        $post_data = $this->getServicePost()->getPostInfos($post_id);
        $identity = $this->getServiceUser()->getIdentity();
        if(!empty($post_data['content'])){
            $mentions = [];
            $users = [];
            preg_match_all ( '/@{user:(\d+)}/', $post_data['content'], $mentions );
            for ($i = 0; $i < count($mentions[0]); $i++) {
                $mention = $mentions[0][$i];
                $uid = $mentions[1][$i];
                if(!isset($users[$uid] )){
                    $users[$uid] = $this->getServiceUser()->getLite($uid);
                }
                if(false !== $users[$uid]){
                    $post_data['content'] = str_replace($mention, strtolower('@'.$users[$uid]->getFirstname().$users[$uid]->getLastName()), $post_data['content']);
                }
            }
            $post_data['content'] = ": &laquo;".$this->limitText($post_data['content'])."&raquo;";
        }

        if(!empty($post_data['picture'])){
            $data['link_picture'] = $post_data['picture'];
        }
        else if(!empty($post_data['image'])){
            $data['image'] = $post_data['image'];
        }
        else if(!empty($post_data['origin']['page']['logo']) && empty($post_data['page']['id'])){
            $data['logo'] = $post_data['origin']['page']['logo'];
        }

        $target = null;
        $picture = null;
        $uid = 'post.'.$action.'.'.$post_id;
        $data['target'] = $post_data['parent']['user']['id'];
        $data['picture'] = !empty($post_data['page']['id']) ? $post_data['page']['logo'] : $post_data['user']['avatar'];

        switch($action){
            case 'like':
              $data['target'] = $post_data['user']['id'];
              $data['picture'] = $identity['avatar'];
            break;
            case 'create':
            case 'tag':
                $data['target'] = $post_data['user']['id'];
                $data['picture'] = !empty($post_data['page']['id']) ? $post_data['page']['logo'] : $post_data['user']['avatar'];
            break;
            case 'request':
            case 'accept':
                $m_user = $this->getServiceUser()->getLite($data['user']);
                $data['target'] = $data['contact'];
                $data['picture'] = !($m_user->getAvatar() instanceof IsNull)? $m_user->getAvatar() : null;
            break;
            case 'com':
                $uid = 'post.'.$action.'.'.$data['parent_id'];
            break;
        }

        $labels = [
            'source' => '<b>'.$identity['firstname'].' '.$identity['lastname']."</b>",
            'post_source' => !empty($post_data['page']['id']) ? ('<b>'.$this->limitText($post_data['page']['title']).'</b>') : ('<b>'.$post_data['user']['firstname'].' '.$post_data['user']['lastname'].'</b>'),
            'post_owner' => !empty($post_data['page']['id']) ?  ('<b>'.$this->limitText($post_data['page']['title'])."</b>'s") :  '{user}',
            'post_action'=> $post_data['parent']['id'] === $post_data['origin']['id'] ? 'commented on' : 'replied to',
            'post_type' => empty($post_data['parent']['id']) ? 'post' : ($post_data['parent']['id'] === $post_data['origin']['id'] ? 'comment' : 'reply'),
            'parent_source' =>  !empty($post_data['parent']['page']['id']) ? ('<b>'.$this->limitText($post_data['parent']['page']['title'])."</b>'s") : "{user}",
            'parent_type' =>  ($post_data['parent']['id'] === $post_data['origin']['id'] ? 'post' : 'comment'),
            'target_page' => !empty($post_data['origin']['page']['id']) ? 'in <b>'.$this->limitText($post_data['origin']['page']['title']).'</b>' : '',
            'content' => $post_data['content']
        ];

        $this->getServiceEvent()->create($post_data['type'], $action, $uid, $libelle, $data, $labels, $notify );

        return true;
    }

    /**
     * Hard Delete Post Subscription
     *
     * @param  string $libelle
     * @param  int    $post_id
     * @return int
     */
    public function delete($post_id, $libelle = null)
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
            if(!empty($m_post_subscription->getLibelle())){
                $lib[] = $m_post_subscription->getLibelle();
            }
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

<?php
/**
 * TheStudnet (http://thestudnet.com).
 *
 * Event
 */
namespace Application\Service;

use Dal\Service\AbstractService;
use Zend\Json\Server\Request;
use Zend\Http\Client;

/**
 * Class Event
 */
class Event extends AbstractService
{
    /**
     * Identification request.
     *
     * @var int
     */
    private static $id = 0;
    
    const TARGET_TYPE_USER = 'user';
    const TARGET_TYPE_GLOBAL = 'global';
    const TARGET_TYPE_SCHOOL = 'school';
    
    /**
     * Envoie une notification "notification.publish" sans l'enregistrer dans la table event
     *
     * @param mixed  $data
     * @param string $type
     * @param array  $libelle
     *
     * @return array get users sended
     */
    public function sendData($data, $type, $libelle, $source = null, $object = null, $date = null)
    {
        $users = $this->getServiceSubscription()->getListUserId($libelle);
        $rep = false;
        if (count($users) > 0) {
            try {
                $data = ['notification' => ['data' => $data,'event' => $type,'source' => $source, 'object' => $object],'users' => array_values($users),'type' => self::TARGET_TYPE_USER];
                $rep = $this->nodeRequest('notification.publish', $data);
                if ($rep->isError()) {// @codeCoverageIgnore
                    throw new \Exception('Error jrpc nodeJs: ' . $rep->getError()->getMessage(), $rep->getError()->getCode());// @codeCoverageIgnore
                }
            } catch (\Exception $e) {
                syslog(1, 'SendData : ' . json_encode($data));
                syslog(1, $e->getMessage());
            }
        }
        
        return $users;
    }
    
    /**
     * Envent request nodeJs
     *
     * @param string $method
     * @param array $params
     *
     * @return \Zend\Json\Server\Response
     */
    public function nodeRequest($method, $params = null)
    {
        if(!isset($params['notification'])) {
            $params['notification'] = null;
        }
        
        $params['notification']['nid'] = uniqid('notif', true);
        
        $request = new Request();
        $request->setMethod($method)
            ->setParams($params)
            ->setId(++ self::$id)
            ->setVersion('2.0');
        
        $authorization = $this->container->get('config')['node']['authorization'];
        $client = new Client();
        $client->setOptions($this->container->get('config')['http-adapter']);
        $client->setHeaders([ 'Authorization' => $authorization]);
        
        return (new \Zend\Json\Server\Client($this->container->get('config')['node']['addr'], $client))->doRequest($request);
    }
    
    /**
     * create un event puis envoye un evenement "notification.publish"
     *
     * @param  string $event    nom de l'evenement
     * @param  mixed  $source   
     * @param  mixed  $object
     * @param  array  $user
     * @param  mixed  $target   la source soit user soit school soit global 
     * @param  mixed  $user_id  l'id de la personne qui a généré l'event
     *
     * @throws \Exception
     *
     * @return int
     */
    public function create($event, $source, $object, $libelle, $target, $user_id = null)
    {
        $date = (new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s');
        $m_event = $this->getModel()
            ->setUserId($user_id)
            ->setEvent($event)
            ->setSource(json_encode($source))
            ->setObject(json_encode($object))
            ->setTarget($target)
            ->setDate($date);
        
        if ($this->getMapper()->insert($m_event) <= 0) {
            throw new \Exception('error insert event');// @codeCoverageIgnore
        }
        
        $event_id = $this->getMapper()->getLastInsertValue();
        $this->getServiceEventSubscription()->add($libelle, $event_id);

        $user = $this->sendData(null, $event, $libelle, $source, $object, (new \DateTime($date))->format('Y-m-d\TH:i:s\Z'));
        if (count($user) > 0) {
            foreach ($user as $uid) {
                $this->getServiceEventUser()->add($event_id, $uid);
            }
        }
        
        return $event_id;
    }
    
    /**
     * Event user.publication
     *
     * @param  int   $post_id
     * @param  array $sub
     * 
     * @return int
     */
    public function userPublication($sub, $post_id, $type = 'user', $ev = 'publication', $src = null)
    {
        $user_id = $this->getServiceUser()->getIdentity()['id'];
        $data_post = $this->getDataPost($post_id);
        $event = $type;
        if (is_string($ev)) {
            $event .= '.'.$ev;
        }
        
        return $this->create($event, $this->getDataUser($src), $data_post, $sub, self::TARGET_TYPE_USER, $user_id);
    }
    
    /**
     * Get events list for current user.
     *
     * @param array $filter
     *
     * @invokable
     *
     * @return array
     */
    public function getList($filter, $events = null, $unread = null){
        $mapper = $this->getMapper();
        $res_event = $mapper->usePaginator($filter)->getList($this->getServiceUser()->getIdentity()['id'], $events, $unread);
        return [ 'list' => $res_event, 'count' => $mapper->count()];
    }
    
    /**
     * Get events list for current user.
     *
     * @param array|int $id
     *
     * @invokable
     *
     * @return array
     */
    public function read($id = null) {
        if(null !== $id && !is_array($id)){
            $id = [$id];
        }
        return $this->getServiceEventUser()->read($id);
    }
    
   
    
    // ------------- DATA OBJECT -------------------

    /**
     * Get Data Post
     *
     * @param  int $post_id
     * @return array
     */
    private function getDataPost($post_id)
    {
        $ar_post = $this->getServicePost()->getLite($post_id)->toArray();
        $ar_data = [
            'id' => $ar_post['id'],
            'name' => 'post',
            'data' => [
                'id' =>  $ar_post['id'],
                'content' => $ar_post['content'],
                'picture' => $ar_post['picture'],
                'name_picture' => $ar_post['name_picture'],
                'link' => $ar_post['link'],
                't_page_id' => $ar_post['t_page_id'],
                't_user_id' => $ar_post['t_user_id'],
                'parent_id' => $ar_post['parent_id'],
                'origin_id' => $ar_post['origin_id'],
                'type' => $ar_post['type'],
            ]
        ];

        if(null !== $ar_post['t_page_id']){
            $ar_page = $this->getServicePage()->getLite($ar_post['t_page_id']);
            $ar_data['data']['page'] = $ar_page;
        }

        return $ar_data;
    }

    /**
     * Get Data User.
     *
     * @param int $user_id
     *
     * @return array
     */
    public function getDataUser($user_id = null)
    {
        if (null == $user_id) {
            $identity = $this->getServiceUser()->getIdentity();
            if ($identity === null) {
                return [];// @codeCoverageIgnore
            }
            $user_id = $identity['id'];
        }

        $m_user = $this->getServiceUser()->get($user_id);

        return ['id' => $user_id,
            'name' => 'user','data' =>
            ['firstname' => $m_user['firstname'],'email' => $m_user['email'],'lastname' => $m_user['lastname'],'nickname' => $m_user['nickname'],'gender' =>
                $m_user['gender'],
                'has_email_notifier' => $m_user['has_email_notifier'],
                'avatar' => $m_user['avatar'],
                'organization' => $m_user['organization_id'],
                'user_roles' => $m_user['roles']]];
    }

    // ------------------------------------------------- Methode

    /**
     * Get Service Event Comment.
     *
     * @return \Application\Service\EventSubscription
     */
    private function getServiceEventSubscription()
    {
        return $this->container->get('app_service_event_subscription');
    }

    /**
     * Get Service Event User.
     *
     * @return \Application\Service\EventUser
     */
    private function getServiceEventUser()
    {
        return $this->container->get('app_service_event_user');
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
     * Get Service Post
     *
     * @return \Application\Service\Post
     */
    private function getServicePost()
    {
        return $this->container->get('app_service_post');
    }

    /**
     * Get Service Post
     *
     * @return \Application\Service\Page
     */
    private function getServicePage()
    {
        return $this->container->get('app_service_page');
    }

    /**
     * Get Service Subscription
     *
     * @return \Application\Service\Subscription
     */
    private function getServiceSubscription()
    {
        return $this->container->get('app_service_subscription');
    }
}

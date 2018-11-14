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
    public function create($event, $source, $object, $libelle, $target, $user_id = null, $fcm_package = null, $send_email = false, $force_email = false)
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
        $data = ['event' => $event];
        if($object['name'] === 'post'){
            $data['initial'] = $this->getDataPost($object['id']);
            if(isset($data['initial']['parent_id'])){
                $data['parent'] = $this->getDataPost($data['initial']['parent_id']);
            }
            if(isset($data['initial']['origin_id'])){
                $data['origin'] = $this->getDataPost($data['initial']['origin_id']);
            }
            if(isset($data['initial']['shared_id'])){
                $data['shared'] = $this->getDataPost($data['initial']['shared_id']);
            }
        }
        else{
            if(isset($data['object']['data']) && (isset($data['object']['data']['t_page_id']) || isset($data['object']['page_id']))){
                $data['target'] = $this->getServicePage()->getLite(isset($data['object']['page_id']) ? $data['object']['page_id'] : $data['object']['data']['t_page_id'])->toArray();
            }
            if(isset($data['object']['data']) && isset($data['object']['data']['user_id'])){
                $data['user'] = $this->getServiceuser()->getLite($data['object']['data']['user_id'])->toArray();
            }
        }
        $user = $this->sendData(null, $event, $libelle, $source, $object, (new \DateTime($date))->format('Y-m-d\TH:i:s\Z'));
        if (count($user) > 0) {
            foreach ($user as $uid) {
                $m_event_user = $this->getServiceEventUser()->add($event_id, $uid, $source, $data);
                if(false !== $m_event_user && null !== $fcm_package){
                    $fcm_service = $this->getServiceFcm();
                    $gcm_notification = new GcmNotification();
                    $gcm_notification->setTitle($m_page->getTitle())
                        ->setSound("default")
                        ->setColor("#00A38B")
                        ->setIcon("icon")
                        ->setTag("TWIC:".$event)
                        ->setBody(strip_tags ($m_event_user->getText()));

                    $this->getServiceFcm()->send($uid, null, $gcm_notification, $fcm_package );
                }
                if(true === $send_email){
                    $this->sendRecapEmail($uid, $force_email);
                }
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
    public function getList($filter, $events = null, $unread = null, $start_date = null){
        return $this->_getList($filter, $events, $unread, $this->getServiceUser()->getIdentity()['id'], $start_date);
    }

    public function _getList($filter, $events = null, $unread = null, $user_id = null, $start_date = null){
        $mapper = $this->getMapper();
        $res_event = $mapper->usePaginator($filter)->getList($this->getServiceUser()->getIdentity()['id'], $events, $unread, $start_date);
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
                'user_id' => $ar_post['user_id'],
                'parent_id' => $ar_post['parent_id'],
                'origin_id' => $ar_post['origin_id'],
                'shared_id' => $ar_post['shared_id'],
                'page_id' => $ar_post['page_id'],
                'type' => $ar_post['type'],
            ]
        ];

        if(null !== $ar_post['page_id']){
            $ar_page = $this->getServicePage()->getLite($ar_post['t_page_id'])->toArray();
            $ar_data['data']['page'] = $ar_page;
        }

        if(null !== $ar_post['t_page_id']){
            $ar_page = $this->getServicePage()->getLite($ar_post['t_page_id'])->toArray();
            $ar_data['data']['target'] = $ar_page;
        }

        if(null !== $ar_post['user_id']){
            $ar_page = $this->getServiceuser()->getLite($ar_post['user_id'])->toArray();
            $ar_data['data']['user'] = $ar_page;
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


    function limit($text, $length = 50){
        return strlen($text) > $length ? substr($text, $length).'...' : $text;
    }

    function sendRecapEmail($user_id, $last_activity='event', $force = false){
        $m_activity = $this->getServiceActivity()->getList(['n'=>1, 'p'=>1, 'o' => 'activity$id DESC'], null, null, null, $user_id)->current();
        if($force || (false !== $m_activity && time() - strtotime($m_activity->getDate() > 60 * 60 * 24 * 7) )){
            $labels = [
                //ALL
                'display_all' => 'none',
                //MAIN NOTIFICATION
                'ntf_picture' => '',
                'ntf_text' => '',
                //LIKE
                'display_like' => 'none',
                'like_text' => '',
                'like_picture' => '',
                'display_like_count'  => 'none',
                'like_count'  => 0,
                //REQUEST
                'display_request' => 'none',
                'request_text' => '',
                'request_picture' => '',
                'display_request_count'  => 'none',
                'request_count'  => 0,
                //CHAT
                'display_chat' => 'none',
                'chat_text' => '',
                'chat_picture' => '',
                'display_chat_count'  => 'none',
                'chat_count'  => 0,
                //POST
                'display_post' => 'none',
                'post_text' => '',
                'post_picture' => '',
                'display_post_count'  => 'none',
                'post_count'  => 0,
                //COM
                'display_comment' => 'none',
                'comment_text' => '',
                'comment_picture' => '',
                'display_comment_count'  => 'none',
                'comment_count'  => 0,
                //USERS
                'display_user' => 'none',
                'user_text' => '',
                'user_picture' => '',
                'display_user_count'  => 'none',
                'user_count'  => 0,
            ];


            $res_event = $this->_getList([ 'o' => 'event.id DESC', 'c' => ['event.date' => '>'], 's' => $m_activity->getDate()], null, null, $user_id);
            $res_message = $this->getServiceMessage()->getList($user_id, null, ['n' => 1, 'p' => 1, 'o' => 'message$id DESC'], true);
            $res_request = $this->getServiceContact()->getListRequest($user_id, null, ['n' => 1, 'p' => 1, 'o' => 'message$id DESC'], true);
            $res_user = $this->getServiceUser()->getListId(null, null, ['c' => ['user.created_date' => '>'], 's' => $m_activity->getDate(), 'p' => 1, 'o' => 'user$id DESC'], null, null, null, null, null, null, null, null, null, true);
            $urldms = $this->container->get('config')['app-conf']['urldms'];

            foreach($res_event as $m_event){
                if(empty($labels['ntf_text']) && $last_activity === 'event'){
                    $labels['ntf_text'] = $m_event->getText();
                    $labels['ntf_picture'] = $urldms.$m_event->getPicture().'-80m80';
                }
                else{
                     if($m_event->getEvent() === 'post.like'){
                        $labels['display_all'] = 'block';
                        $labels['display_like'] = 'block';
                        if(empty($labels['like_text'])){
                            $labels['like_text'] = $m_event->getText();
                            $labels['like_picture'] = $urldms.$m_event->getPicture().'-80m80';
                        }
                        else{
                            $labels['like_count']++;
                            $labels['display_like_count']  = 'block';
                        }
                    }
                    if($m_event->getEvent() === 'post.create'){
                       $labels['display_all'] = 'block';
                       $labels['display_post'] = 'block';
                       if(empty($labels['post_text'])){
                           $labels['post_text'] = $m_event->getText();
                           $labels['post_picture'] = $urldms.$m_event->getPicture().'-80m80';
                       }
                       else{
                           $labels['post_count']++;
                           $labels['display_post_count']  = 'block';
                       }
                   }
                   if($m_event->getEvent() === 'post.com'){
                      $labels['display_all'] = 'block';
                      $labels['display_comment'] = 'block';
                      if(empty($labels['comment_text'])){
                          $labels['comment_text'] = $m_event->getText();
                          $labels['comment_picture'] = $urldms.$m_event->getPicture().'-80m80';
                      }
                      else{
                          $labels['comment_count']++;
                          $labels['display_comment_count']  = 'block';
                      }
                   }
                }
            }
            if($res_request->count() > 0){
                 $m_request = $res_request->current();
                 $m_contact = $this->getServiceUser()->getLite($m_request->getUserId());
                 if($last_activity  === 'request' && empty($labels['ntf_text'])){
                     $labels['ntf_text'] = '<b>'.$m_contact->getFirstname().' '.$m_contact->getLastname().'</b> sent you a connection request.';
                     $labels['ntf_picture'] =  $urldms.$m_contact->getAvatar().'-80m80';
                 }
                 $idx = $last_activity  === 'request' ? 1 : 0;
                 if($res_request->count() > $idx){
                     $labels['display_all'] = 'block';
                     $labels['display_request'] = 'block';
                     $res_request->next();
                     $m_request = $res_request->current();
                     $m_contact = $this->getServiceUser()->getLite($m_request->getUserId());
                     $labels['request_text'] = '<b>'.$m_contact->getFirstname().' '.$m_contact->getLastname().'</b> sent you a connection request.';
                     $labels['request_picture'] =  $urldms.$m_contact->getAvatar().'-80m80';
                 }
                 if($res_request->count() > $idx + 1){
                     $labels['request_count'] = $res_request->count() - $idx - 1;
                     $labels['display_request_count']  = 'block';

                 }
            }
            if($res_message->count() > 0){
                $m_message = $res_request->current();
                $m_contact = $this->getServiceUser()->getLite($m_message->getUserId());
                if($last_activity  === 'chat' && empty($labels['ntf_text'])){
                    if($m_message->getText() instanceof IsNull){
                       $labels['ntf_text'] = '<b>'.$m_contact->getFirstname().' '.$m_contact->getLastname().'</b> shared a file';

                    }
                    else{
                       $labels['ntf_text'] =  'You have an unread message from <b>'.$m_contact->getFirstname().' '.$m_contact->getLastname().'</b> : &laquo;'.$this->limit($m_message->getText())."&raquo;";
                    }
                    $labels['ntf_picture'] = $urldms.$m_contact->getAvatar().'-80m80';
                }
                $idx = $last_activity  === 'chat' ? 1 : 0;
                if($res_message->count() > $idx){
                   $labels['display_all'] = 'block';
                   $labels['display_chat'] = 'block';
                   $res_message->next();
                   $m_message = $res_request->current();
                   $m_contact = $this->getServiceUser()->getLite($m_message->getUserId());
                   if($m_message->getText() instanceof IsNull){
                        $labels['chat_text'] = '<b>'.$m_contact->getFirstname().' '.$m_contact->getLastname().'</b> shared a file';

                   }
                   else{
                      $labels['chat_text'] =  'You have an unread message from <b>'.$m_contact->getFirstname().' '.$m_contact->getLastname().'</b> : &laquo;'.$this->limit($m_message->getText())."&raquo;";
                   }
                   $labels['chat_picture'] = $urldms.$m_contact->getAvatar().'-80m80';
                }
                if($res_request->count() > $idx + 1){
                   $labels['chat_count'] = $res_request->count() - $idx - 1;
                   $labels['display_chat_count']  = 'block';
                }
            }

            if($res_user->count() > 0){
               $user_id = $res_user->current();
               $labels['display_all'] = 'block';
               $labels['display_user'] = 'block';
               $m_user = $this->getServiceUser()->getLite($m_message->getUserId());
               if($m_message->getText() instanceof IsNull){
                    $labels['chat_text'] = '<b>'.$m_contact->getFirstname().' '.$m_contact->getLastname().'</b> shared a file';

               }
               $labels['user_text'] =  $m_user->getFirstname().' '.$m_user->getLastname().' joined TWIC';
               $labels['user_picture'] = $urldms.$m_user->getAvatar().'-80m80';
               if($res_request->count() > $idx + 1){
                  $labels['user_count'] = $res_request->count() - $idx - 1;
                  $labels['display_user_count']  = 'block';
               }
            }
            $m_user = $this->getServiceUser()->getLite($user_id);
            $this->getServiceMail()->sendTpl('tpl_newactivity', $m_user->getEmail(), $labels);
        }
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
     * Get Service Activity.
     *
     * @return \Application\Service\Activity
     */
    private function getServiceActivity()
    {
        return $this->container->get('app_service_activity');
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
     * Get Service Message
     *
     * @return \Application\Service\Message
     */
    private function getServiceMessage()
    {
        return $this->container->get('app_service_message');
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

      /**
       * Get Service Service Conversation User.
       *
       * @return \Application\Service\Fcm
       */
      private function getServiceFcm()
      {
          return $this->container->get('fcm');
      }
}

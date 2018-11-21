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
use Zend\Db\Sql\Predicate\IsNull;
use ZendService\Google\Gcm\Notification as GcmNotification;

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
     * @param array  $source
     * @param array  $object
     * @param string  $date
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


    //Event text building
    function getUsername($source){
        return  $source['firstname'] .  " " . $source['lastname'];
    }

    function limitText($text, $length = 50){
        return strlen($text) > $length ? substr($text, 0,  $length).'...' : $text;
    }

    function getContent($content){
        if(empty($content)){
            return "";
        }
        else{
            $mentions = [];
            $users = [];
            preg_match_all ( '/@{user:(\d+)}/', $content, $mentions );
            for ($i = 0; $i < count($mentions[0]); $i++) {
                $mention = $mentions[0][$i];
                $uid = $mentions[1][$i];
                if(!isset($users[$uid] )){
                    $users[$uid] = $this->getServiceUser()->getLite($uid);
                }
                if(false !== $users[$uid]){
                    $content = str_replace($mention, strtolower('@'.$users[$uid]->getFirstname().$users[$uid]->getLastName()), $content);
                }
            }
            return ": &laquo;".$this->limitText($content)."&raquo;";
        }
    }



    function getText($event, $d){
        switch($event){
            case 'post.create':
                return sprintf('%s just posted %s%s', $d['post_source'], $d['target_page'], $d['content']);
            case 'post.com':
                return sprintf('%s %s %s %s %s%s', $d['post_source'], $d['post_action'], $d['parent_source'], $d['parent_type'], $d['target_page'], $d['content']);
            case 'post.like':
                return sprintf('%s liked %s %s %s%s', $d['source'], $d['post_owner'], $d['post_type'],  $d['target_page'], $d['content']);
            case 'post.tag':
                return sprintf('%s mentionned you in a %s %s%s', $d['post_source'], $d['post_type'], $d['target_page'], $d['content']);
            case 'post.share':
                return sprintf('%s shared %s post %s%s', $d['post_source'], $d['parent_source'], $d['target_page'], $d['content']);
            case 'item.publish':
                return sprintf('<b>%s</b> %s has been published on <b>%s</b>', $d['itemtitle'],$d['itemtype'], $d['pagetitle']);
            case 'item.update':
                return sprintf('<b>%s</b> %s has been updated on <b>%s</b>', $d['itemtitle'],$d['itemtype'], $d['pagetitle']);
            case 'connection.request':
                return sprintf('<b>%s</b>%s sent you a connection request', $d['user'],$d['others']);
            case 'connection.accept':
                return sprintf('<b>%s</b> accepted your connection request', $d['user']);

        }
    }

    function getCount($event, $count){
        switch($event){
            case 'connection.request':
                return "";
            default:
                return sprintf('And <b>%s</b> more...', $count);
        }
    }


    /**
     * create un event puis envoye un evenement "notification.publish"
     *
     * @param  string $type    Event type
     * @param  string $action    Event action
     * @param  mixed $data    Event data
     * @param  array|string  $libelle Subscription to event
     * @param  mixed  $notify medium used to notify ['ntf' => true/false => "Enable/disable notification bell in header", 'fcm' => null/package => "Package to send for fcm", 'mail' => false/int => "Dont 'send or inactivity days required to send an email"]
     *
     * @throws \Exception
     *
     * @return int
     */
    public function create($type, $action, $data, $libelle, $notify = null)
    {
        if(null === $notify){
            $notify = ['ntf' => true, 'fcm' => false, 'mail' => 7];
        }
        else if(false === $notify){
            $notify = ['ntf' => false, 'fcm' => false, 'mail' => false];
        }
        $event = $type.'.'.$action;
        $date = (new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s');
        $identity = $this->getServiceUser()->getIdentity();
        $source = $this->getDataUser();
        $m_event = $this->getModel()
            ->setUserId($identity['id'])
            ->setEvent($event)
            ->setSource(json_encode($source))
            ->setObject(json_encode($data))
            ->setTarget(self::TARGET_TYPE_USER)
            ->setDate($date);
        $ar_data = [];
        if($type === 'post'){
            $ar_data = $this->getServicePost()->getPostInfos($data['id']);
            $data = [
                'source' => '<b>'.$this->getUsername($identity)."</b>",
                'post_source' => !empty($ar_data['page']['id']) ? ('<b>'.$this->limitText($ar_data['page']['title']).'</b>') : ('<b>'.$this->getUsername($ar_data['user']).'</b>'),
                'post_owner' => $ar_data['user']['id'] === $identity['id'] ? 'their' : (!empty($ar_data['page']['id']) ? ('<b>'.$this->limitText($ar_data['page']['title']).'</b>') : '{user}'),
                'post_action'=> $ar_data['type'] === 'reply' ? 'replied to' : 'commented on',
                'post_type' => $ar_data['type'],
                'parent_source' =>  !empty($ar_data['parent']['page']['id']) ? ('<b>'.$this->limitText($ar_data['parent']['page']['title'])."</b>'s") : "{user}",
                'parent_type' => $ar_data['type'] === 'comment' ? 'post' : 'comment',
                'target_page' => !empty($ar_data['origin']['page']['id']) ? 'in <b>'.$this->limitText($ar_data['origin']['page']['title']).'</b>' : '',
                'content' => $this->getContent($ar_data['content'])
            ];

        }
        else if($type === 'connection'){
            $res_request = $this->getServiceContact()->getListRequestId($data['contact']);
            $nb_requests = count($res_request);
            $ar_data = $this->getServiceUser()->getLite($data['user'])->toArray();

            $data = [
                'user' => $this->getUsername($ar_data),
                'contact' => $data['contact'],
                'others' => $nb_requests > 1 ? (' and '.($nb_requests - 1). ' other'.($nb_requests > 2 ? 's' : '')) : ''
            ];
        }
        else{
            $data = $object;
            $data['source'] = '<b>'.$this->getUsername($identity)."</b>";
        }
        $m_event->setText($this->getText($event, $data));
        $target = null;
        switch($event){
            case 'post.tag':
            case 'post.create':
                $target = $ar_data['user'];
                $m_event->setPicture( !empty($ar_data['page']['id']) ? $ar_data['page']['logo'] : $ar_data['user']['avatar']);
            break;
            case 'post.like':
                $target = $ar_data['user'];
                $m_event->setPicture($source['data']['avatar']);
            break;
            case 'item.publish':
            case 'item.update':
                $target = $identity;
                $m_event->setPicture(  !empty($data['pagelogo']) ? $data['pagelogo'] : null);
            break;
            case 'connection.accept':
            case 'connection.request':
                $target = ['id' => $data['contact']];
                $m_event->setPicture(  !empty($ar_data['avatar']) ? $ar_data['avatar'] : null);
            break;
            default:
                $target = $ar_data['parent']['user'];
                $m_event->setPicture( !empty($ar_data['page']['id']) ? $ar_data['page']['logo'] : $ar_data['user']['avatar']);
            break;
        }
        $m_event->setTargetId($target['id']);
        if ($this->getMapper()->insert($m_event) <= 0) {
            throw new \Exception('error insert event');// @codeCoverageIgnore
        }


        $m_event->setId($this->getMapper()->getLastInsertValue());
        $this->getServiceEventSubscription()->add($libelle, $m_event->getId());

        if(null !== $m_event->getText()){
            $user = $this->sendData(null, $event, $libelle, $source,
                                    ['text' => $m_event->getText(), 'target' => $m_event->getTargetId(), 'picture' => $m_event->getPicture() ],
                                    (new \DateTime($date))->format('Y-m-d\TH:i:s\Z'));
            if (count($user) > 0) {
                foreach ($user as $uid) {
                    if($uid === $identity['id']){
                        continue;
                    }
                    $gcm_text = str_replace('{user}', $m_event->getTargetId() === $uid ? 'your' : ('<b>'.$this->getUsername($target)."</b>'s"), $m_event->getText());

                    if(false !== $notify['ntf']){
                       $m_event_user = $this->getServiceEventUser()->add($m_event->getId(), $uid, $source, $data);
                    }
                    if(false !== $notify['fcm']){

                        try{
                              $fcm_service = $this->getServiceFcm();
                              $gcm_notification = new GcmNotification();
                              $gcm_notification->setTitle("TWIC")
                                  ->setSound("default")
                                  ->setColor("#00A38B")
                                  ->setIcon("icon")
                                  ->setTag("TWIC:".$event)
                                  ->setBody(strip_tags(htmlspecialchars_decode($gcm_text)));

                              $this->getServiceFcm()->send($uid, null, $gcm_notification, $notify['fcm'] );
                        }
                        catch (\Exception $e) {
                            syslog(1, 'GCM Notification : ' . json_encode($gcm_text));
                            syslog(1, $e->getMessage());
                        }
                    }
                }
                if(false !== $notify['mail']){
                    $this->sendRecapEmail($user, $event, $notify['mail']);
                }
            }
        }
        return $m_event->getId();
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
        $res_event = $mapper->usePaginator($filter)->getList($user_id, $events, $unread, $start_date);
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

    function sendRecapEmail($user_id, $ev, $force = false){

        $urldms = $this->container->get('config')['app-conf']['urldms'];
        $urlui = $this->container->get('config')['app-conf']['uiurl'];
        $res_event =  $this->getMapper()->getListUnseen($user_id);

        $ar_events = [];
        foreach($res_event as $m_event){
            if(!isset($ar_events[$m_event->getUserId()])){
                $ar_events[$m_event->getUserId()] = [];
            }
            $ar_events[$m_event->getUserId()][] = $m_event->toArray();
        }
        $users = [];
        $organizations = [];
        if(count($ar_events) > 0){
            $users = $this->getServiceUser()->getLite(array_keys($ar_events));
            $oid = [];
            foreach($users as $uid => $m_user){
                if(!in_array($m_user->getOrganizationId(), $oid)){
                    $oid[] = $m_user->getOrganizationId();
                }
            }
            if(count($oid) > 0){
                $organizations = $this->getServicePage()->getLite($oid);
            }
        }

        foreach($ar_events as $uid => $events){
            $m_user = $users[$uid];
            $organization = $organizations[$m_user->getOrganizationId()];
            $labels = [
                //MAIN NOTIFICATION
                'ntf_picture' => '',
                'ntf_text' => '',
                'ntf_count' => '',
                //NTF1
                'ntf1_display' => 'none',
                'ntf1_picture' => '',
                'ntf1_text' => '',
                'ntf1_count' => '',
                'ntf1_date' => '',
                'ntf1_icon' => '',
                //NTF2
                'ntf2_display' => 'none',
                'ntf2_picture' => '',
                'ntf2_text' => '',
                'ntf2_count' => '',
                'ntf2_date' => '',
                'ntf2_icon' => '',
                //NTF3
                'ntf3_display' => 'none',
                'ntf3_picture' => '',
                'ntf3_text' => '',
                'ntf3_count' => '',
                'ntf3_date' => '',
                'ntf3_icon' => '',
                //NTF4
                'ntf4_display' => 'none',
                'ntf4_picture' => '',
                'ntf4_text' => '',
                'ntf4_count' => '',
                'ntf4_date' => '',
                'ntf4_icon' => '',
                //NTF5
                'ntf5_display' => 'none',
                'ntf5_picture' => '',
                'ntf5_text' => '',
                'ntf5_count' => '',
                'ntf5_date' => '',
                'ntf5_icon' => '',
            ];
            $idx = 1;
            foreach($events as $event){
                if($event['event'] === $ev){
                    $labels['ntf_picture'] =  (null !== $event['picture']) ? ($urldms.$event['picture'].'-80m80') : null;
                    $labels['ntf_text'] = $event['text'];
                    $labels['title'] = strip_tags(htmlspecialchars_decode($event['text']));
                    $labels['ntf_count'] = $event['count']  > 1 ? $this->getCount($event, $event['count']) : '';
                }
                else if($idx < 6){
                      $labels['ntf'.$idx.'_display'] = 'block';
                      $labels['ntf'.$idx.'_picture'] = (null !== $event['picture']) ? ($urldms.$event['picture'].'-80m80') : null;
                      $labels['ntf'.$idx.'_text'] = $event['text'];
                      $labels['ntf'.$idx.'_count'] = $event['count']  > 1 ? $this->getCount($event, $event['count']) : '';
                      $labels['ntf'.$idx.'_icon'] = $organization->getLibelle().".".$urlui.'/assets/img/mail/'.$event['event'].'.png';
                      $labels['ntf'.$idx.'_date'] = $event['date'];
                      $idx++;
                }
            }
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

      /**
       * Get Service Contact.
       *
       * @return \Application\Service\Contact
       */
      private function getServiceContact()
      {
          return $this->container->get('app_service_contact');
      }

      /**
       * Get Service Mail.
       *
       * @return \Mail\Service\Mail
       */
      private function getServiceMail()
      {
          return $this->container->get('mail.service');
      }
}

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

    function getText($event, $d){
        switch($event){
            case 'post.create':
                return sprintf('%s just posted %s%s', $d['post_source'], $d['target_page'], $d['content']);
            case 'post.com':
                return sprintf('%s{more} %s %s %s %s%s', $d['post_source'], $d['post_action'], $d['parent_source'], $d['parent_type'], $d['target_page'], $d['content']);
            case 'post.like':
                return sprintf('%s{more} liked %s %s %s%s', $d['source'], $d['post_owner'], $d['post_type'],  $d['target_page'], $d['content']);
            case 'post.tag':
                return sprintf('%s mentionned you in a %s %s%s', $d['post_source'], $d['post_type'], $d['target_page'], $d['content']);
            case 'post.share':
                return sprintf('%s {more} shared %s post %s%s', $d['post_source'], $d['parent_source'], $d['target_page'], $d['content']);
            case 'item.publish':
                return sprintf('A new %s <b>%s</b> has been published in <b>%s</b>', $d['itemtype'], $d['itemtitle'],$d['pagetitle']);
            case 'section.publish':
                return sprintf('A new %s <b>%s</b> has been published in <b>%s</b>', $d['itemtype'], $d['itemtitle'], $d['pagetitle']);
            case 'item.update':
                return sprintf('%s <b>%s</b> has been updated in <b>%s</b>', $d['itemtype'],$d['itemtitle'], $d['pagetitle']);
            case 'connection.request':
                return sprintf('%s{more} sent you a connection request', $d['source']);
            case 'connection.accept':
                return sprintf('You are now connected with %s', $d['source']);
            case 'user.follow':
                return sprintf( $d['contact_state'] === 0 ? '%s is following you' : 'You are now connected with %s', $d['source']);
            case 'message.send':
                return sprintf('<b>%s</b>{more} sent you a message %s', $d['user'], $d['text']);
            case 'page.doc':
                return sprintf('A new material : <b>%s</b> has been added in <b>%s</b>', $d['library_name'], $d['page_title']);
            case 'page.member':
                return sprintf('You are enrolled in <b>%s</b>',  $d['page_title']);
            case 'page.invited':
                return sprintf('You are invited to join <b>%s</b>', $d['page_title']);
            case 'page.pending':
                return sprintf('<b>%s</b> requested to join <b>%s</b>', $d['source'], $d['page_title']);
        }
    }


        function getCTAText($event){
            switch($event){
                case 'post.create':
                case 'post.com':
                case 'post.like':
                case 'post.tag':
                case 'post.share':
                    return "View post";
                case 'item.publish':
                case 'section.publish':
                case 'item.update':
                    return "View item";
                case 'connection.request':
                case 'connection.accept':
                case 'user.follow':
                    return "View profile";
                case 'message.send':
                    return "View message";
                case 'page.doc':
                    return "View material";
                case 'page.member':
                case 'page.invited':
                case 'page.pending':
                    return "View";
            }
        }


    function formatText($text, $user_id, $me, $target){
        $text = str_replace('{more}', '', $text);
        if(null !== $target){
            if($me === $target->getId()){
                $text = str_replace('{user}',  'their' , $text);
            }
            else if($user_id === $target_id){
                $text = str_replace('{user}',  'your' , $text);
            }
            else{
                $text = str_replace('{user}',  ('<b>'.$target->getFirstname().' '.$target->getLastname()."</b>'s") , $text);
            }
        }
        return strip_tags(html_entity_decode($text));

    }

    function getLink($event, $d){

          $page_type = [
              'event' => 'event',
              'group' => 'club',
              'organization' => 'institution',
              'course' => 'course'
          ];
        switch($event){
            case 'post.create':
            case 'post.tag':
            case 'post.share':
                return sprintf('/dashboard/%s', $d['id']);
            case 'post.com':
            case 'post.like':
                return sprintf('/dashboard/%s', !empty($d['origin_id']) ? $d['origin_id'] : $d['id'] );
            case 'connection.request':
            case 'connection.accept':
                return sprintf('/profile/%s',  $d['user'] );
            case 'item.publish':
            case 'item.update':
                return sprintf('/page/%s/%s/content/%s',  $page_type[$d['page_type']],  $d['page'],  $d['item'] );
            case 'section.publish':
                return sprintf('/page/%s/%s/content/',  $page_type[$d['page_type']],  $d['page']);
            case 'message.send':
                return '';
            case 'page.doc':
                return sprintf('/page/%s/%s/resources/%s',  $page_type[$d['page_type']],  $d['page'],  $d['library'] );
            case 'page.member':
            case 'page.pending':
            case 'page.invited':
                return sprintf('/page/%s/%s/everyone',  $page_type[$d['page_type']],  $d['page'] );
        }
    }

    function getLast($event, $user_id){
        $res_event = $this->getMapper()->getLast($event, $user_id);
        $lasts = [];
        foreach($res_event as $m_event){
            $lasts[$m_event->getUserId()] = $m_event->getId();
        }

        return $lasts;
    }

    /**
     * create un event puis envoye un evenement "notification.publish"
     *
     * @param  string $type    Event type
     * @param  string $action    Event action
     * @param  array|string  $libelle Subscription to event
     * @param  mixed $event_data    Event data
     * @param  mixed $text_data    Text data
     * @param  mixed  $notify medium used to notify ['fcm' => null/package => "Package to send for fcm", 'mail' => false/true => "Request instant email or not"]
     *
     * @throws \Exception
     *
     * @return int
     */
    public function create($type, $action, $libelle, $event_data, $text_data, $notify = null)
    {
        if(null === $notify){
            $notify = ['fcm' => Fcm::PACKAGE_TWIC_APP, 'mail' => false];
        }
        else if(false === $notify){
            $notify = [ 'fcm' => false, 'mail' => false];
        }
        $event = $type.'.'.$action;
        $date = (new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s');

        $user_id = $this->getServiceUser()->getIdentity()['id'];

        $source = $this->getDataUser($user_id);
        $m_event = $this->getModel()
            ->setUserId($user_id)
            ->setEvent($event)
            ->setSource(json_encode($source))
            ->setObject(json_encode($event_data))
            ->setTarget(self::TARGET_TYPE_USER)
            ->setTargetId(isset($event_data['target']) ? $event_data['target'] : null)
            ->setPicture(isset($event_data['picture']) ? $event_data['picture'] : null)
            ->setDate($date)
            ->setAcademic($notify['mail']);

        $m_event->setText($this->getText($event, $text_data));
        $event_data['text'] = $m_event->getText();
        if ($this->getMapper()->insert($m_event) <= 0) {
            throw new \Exception('error insert event');// @codeCoverageIgnore
        }

        $event_id = $this->getMapper()->getLastInsertValue();
        $this->getServiceEventSubscription()->add($libelle, $event_id);
        if(null !== $m_event->getText()){
            $users = $this->sendData(null, $event, $libelle, $source,  $event_data, (new \DateTime($date))->format('Y-m-d\TH:i:s\Z'));
            if (($idx = array_search($user_id, $users)) !== false) {
                unset($users[$idx]);
                $users = array_values($users);
            }
            if (count($users) > 0) {
                $this->getServiceEventUser()->add($event_id, $users);
                if(false !== $notify['fcm']){
                    $this->sendFcmNotifications($users, $event, $m_event->getText(), $m_event->getTargetId(), $notify['fcm']);
                }
                if(true !== $notify['mail']){
                    $users = $this->getServiceActivity()->getListInactive($users, 7);
                }
                $ntf_date = new \DateTime('now', new \DateTimeZone('UTC'));
                $ntf_date->modify('+1 minutes');
                $ntf_data =   [
                    'date' => $ntf_date->format('Y-m-d\TH:i:s\Z'),
                    'uid' => 'mail.send.'.$event_id,
                    'data' => [ 'type' => 'mail.send',
                                'data' => ['users' => json_encode($users)]]
                ];
                if(count($users) > 0){
                    try {
                        $rep = $this->nodeRequest('notification.register', $ntf_data);
                        if ($rep->isError()) {
                            throw new \Exception('Error jrpc nodeJs: ' . $rep->getError()->getMessage(), $rep->getError()->getCode());
                        }
                    } catch (\Exception $e) {
                        syslog(1, 'Request notification.register : ' . json_encode($ntf_data));
                        syslog(1, $e->getMessage());
                        $this->sendRecapEmail($users);
                    }
                }
            }
        }
        return $event_id;
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
                'avatar' => $m_user['avatar'],
                'organization' => $m_user['organization_id'],
                'user_roles' => $m_user['roles']]];
    }


    function limit($text, $length = 50){
        return strlen($text) > $length ? substr($text, $length).'...' : $text;
    }

    function sendFcmNotifications($users, $event, $text, $target_id, $package){
        $target = null;
        $your_text = $text;
        $user_text = $text;
        $me = $this->getServiceUser()->getIdentity()['id'];
        if(null !== $target_id && false !== strpos('{user}', $text)){
            $target = $this->getServiceUser()->getLite($target_id);
        }
        foreach ($users as $uid) {
              $ntf_text =  $this->formatText($text, $uid, $me, $target);
              try{
                    $fcm_service = $this->getServiceFcm();
                    $gcm_notification = new GcmNotification();
                    $gcm_notification->setTitle("TWIC")
                        ->setSound("default")
                        ->setColor("#00A38B")
                        ->setIcon("icon")
                        ->setTag("TWIC:".$event)
                        ->setBody($ntf_text);

                    $this->getServiceFcm()->send($uid, null, $gcm_notification, $package);
              }
              catch (\Exception $e) {
                  syslog(1, 'GCM Notification : ' . $ntf_text);
                  syslog(1, $e->getMessage());
              }
        }
    }

    function sendRecapEmail($users){

        $urldms = $this->container->get('config')['app-conf']['urldms'];
        $urlui = $this->container->get('config')['app-conf']['uiurl'];
        $res_event =  $this->getMapper()->getListUnseen($users);
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
        $mails = [];
        foreach($ar_events as $uid => $events){
            $m_user = $users[$uid];
            $organization = $organizations[$m_user->getOrganizationId()];
            $libelle = $organization->getLibelle() instanceof IsNull ? '' : $organization->getLibelle();
            $labels = [
                'firstname' => $m_user->getFirstname(),
                'ui_url' => sprintf('https://%s.%s',$libelle,$urlui),
                'current_year' => date("Y"),
                'dates' => '',
                'unsubscribe_link' => '',
                'more_display' => 'none',
                //MAIN NOTIFICATION
                'ntf_display' => 'none',
                'ntf_text' => '',
                'ntf_link' => '',
                'ntf_cta' => '',
                'ntf_display_cta' => 'none',
                //NTFS
                'ntfs_display' => 'none',
                //NTF1
                'ntf1_display' => 'none',
                'ntf1_text' => '',
                'ntf1_link' => '',
                'ntf1_cta' => '',
                'ntf1_display_cta' => 'none',
                //NTF2
                'ntf2_display' => 'none',
                'ntf2_text' => '',
                'ntf2_link' => '',
                'ntf2_cta' => '',
                'ntf2_display_cta' => 'none',
                //NTF3
                'ntf3_display' => 'none',
                'ntf3_text' => '',
                'ntf3_link' => '',
                'ntf3_cta' => '',
                'ntf3_display_cta' => 'none',
                //NTF4
                'ntf4_display' => 'none',
                'ntf4_text' => '',
                'ntf4_link' => '',
                'ntf4_cta' => '',
                'ntf4_display_cta' => 'none',
                //NTF5
                'ntf5_display' => 'none',
                'ntf5_text' => '',
                'ntf5_link' => '',
                'ntf5_cta' => '',
                'ntf5_display_cta' => 'none',
            ];
            $idx = 0;
            $academic = 0;
            $date = (new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d\TH:i:s\Z');
            foreach($events as $event){
                if($idx === 0){
                    if($event['academic'] === 1){
                        $labels['ntf_text'] = $event['text'];
                        $labels['ntf_display'] = 'block';
                        $labels['ntf_cta'] = $this->getCTAText($event['event']);
                        $labels['ntf_display_cta'] = 'block';
                        $labels['title'] = strip_tags(html_entity_decode($event['text']));
                        $labels['ntf_link'] =  sprintf('https://%s.%s%s',$libelle, $urlui, $this->getLink($event['event'],json_decode($event['object'], true)));
                        $labels['unsubscribe_link'] =  sprintf('https://%s.%s/unsubscribe/%s',$libelle, $urlui, md5($uid.$event['id'].$event['date'].$event['object']));
                        $academic = 1;
                    }
                    $idx++;
                }
                if($idx < 6 && $event['academic'] === 0){
                      $labels['ntfs_display'] = 'block';
                      if(empty($labels['dates'])){
                          $labels['dates'] = $event['date'];
                      }
                      if(empty($labels['title'])){
                         $labels['title'] = strip_tags(html_entity_decode($event['text']));
                      }
                      if(empty($labels['unsubscribe_link'])){
                          $labels['unsubscribe_link'] =  sprintf('https://%s.%s/unsubscribe/%s',$libelle, $urlui, md5($uid.$event['id'].$event['date'].$event['object']));
                      }
                      $last_date = $event['date'];
                      $labels['ntf'.$idx.'_display'] = 'block';
                      $labels['ntf'.$idx.'_cta'] = $this->getCTAText($event['event']);
                      $labels['ntf'.$idx.'_display_cta'] = 'block';
                      $labels['ntf'.$idx.'_text'] = $event['text'];
                      $labels['ntf'.$idx.'_link'] =  sprintf('https://%s.%s%s',$libelle, $urlui, $this->getLink($event['event'],json_decode($event['object'], true)));
                      $idx++;
                }
                else{
                    $labels['more_display'] = 'block';
                }
            }
            if($last_date !== $labels['dates']){
                $labels['dates'] = $last_date.' - '.$labels['dates'];
            }
            if(($academic === 0 && $m_user->getHasSocialNotifier() === 1) ||
               ($academic === 1 && $m_user->getHasAcademicNotifier() === 1)){
                $mails[$m_user->getEmail()] = $labels;
                $this->getServiceActivity()->_add($date, 'mail', ['name' =>'received', 'data' => $labels ], null, $uid);
            }
        }

        $this->getServiceMail()->sendMultiTpl('tpl_newactivity', $mails);

        return true;
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

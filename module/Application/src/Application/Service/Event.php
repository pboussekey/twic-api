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
                return sprintf('%s %s %s %s %s%s', $d['post_source'], $d['post_action'], $d['parent_source'], $d['parent_type'], $d['target_page'], $d['content']);
            case 'post.like':
                return sprintf('%s liked %s %s %s%s', $d['source'], $d['post_owner'], $d['post_type'],  $d['target_page'], $d['content']);
            case 'post.tag':
                return sprintf('%s mentionned you in a %s %s%s', $d['post_source'], $d['post_type'], $d['target_page'], $d['content']);
            case 'post.share':
                return sprintf('%s shared %s post %s%s', $d['post_source'], $d['parent_source'], $d['target_page'], $d['content']);
            case 'item.publish':
                return sprintf('<b>%s</b> %s has been published in <b>%s</b>', $d['itemtitle'],$d['itemtype'], $d['pagetitle']);
            case 'item.update':
                return sprintf('<b>%s</b> %s has been updated in <b>%s</b>', $d['itemtitle'],$d['itemtype'], $d['pagetitle']);
            case 'connection.request':
                return sprintf('%s sent you a connection request', $d['source']);
            case 'connection.accept':
                return sprintf('You are now connected with %s', $d['source']);
            case 'message.send':
                return sprintf('You have an unread message from <b>%s</b>%s', $d['user'], $d['text']);
            case 'page.doc':
                return sprintf('A new material has been added to in <b>%s</b>', $d['pagetitle']);
            case 'page.member':
                return sprintf('You are enrolled in <b>%s</b>',  $d['pagetitle']);
            case 'page.pending':
                return sprintf('You are invited to join <b>%s</b>', $d['pagetitle']);
            case 'page.invited':
                return sprintf('<b>%s</b> requested to join <b>%s</b>', $d['source'], $d['pagetitle']);
        }
    }

    function getLink($event, $d){
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
                return sprintf('/page/%s%/%s/content/%s',  $d['page_type'],  $d['page_id'],  $d['item_id'] );
            case 'message.send':
                return '';
            case 'page.doc':
                return sprintf('/page/%s%/%s/resources/%s',  $d['page_type'],  $d['page_id'],  $d['library'] );
            case 'page.member':
            case 'page.pending':
            case 'page.invited':
                $page_type = [
                    'event' => 'event',
                    'group' => 'club',
                    'organization' => 'institution',
                    'course' => 'course'
                ];
                return sprintf('/page/%s%/%s/everyone',  $page_type[$d['page_type']],  $d['page_id'] );
        }
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
    public function create($type, $action,  $libelle, $event_data, $text_data, $notify = null)
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
            ->setImportant($notify['mail']);

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
            }
            if (count($users) > 0) {
                foreach ($users as $uid) {
                    $this->getServiceEventUser()->add($event_id, $uid, $source, $event_data);
                }
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
        if(null !== $target_id && false !== strpos('{user}', $text)){
            $target = $this->getServiceUser()->getLite($target_id);
            $your_text = strip_tags(html_entity_decode(str_replace('{user}', 'your' , $your_text)));
            $user_text = strip_tags(html_entity_decode(str_replace('{user}', ('<b>'.$target->getFirstname().' '.$target->getLastname()."</b>'s"), $user_text)));
        }
        foreach ($users as $uid) {
              $ntf_text = $target_id === $uid ? $your_text : $user_text;
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
                'img_folder' => sprintf('https://%s.%s',$libelle,$urlui),
                'current_year' => date("Y"),
                //MAIN NOTIFICATION
                'ntf_picture' => '',
                'ntf_display_picture' => 'none',
                'ntf_text' => '',
                'ntf_link' => '',
                'ntf_count' => '',
                //NTF1
                'ntf1_display' => 'none',
                'ntf1_picture' => '',
                'ntf1_display_picture' => 'none',
                'ntf1_text' => '',
                'ntf1_link' => '',
                'ntf1_count' => '',
                'ntf1_date' => '',
                'ntf1_icon' => '',
                //NTF2
                'ntf2_display' => 'none',
                'ntf2_picture' => '',
                'ntf2_display_picture' => 'none',
                'ntf2_text' => '',
                'ntf2_link' => '',
                'ntf2_count' => '',
                'ntf2_date' => '',
                'ntf2_icon' => '',
                //NTF3
                'ntf3_display' => 'none',
                'ntf3_picture' => '',
                'ntf3_display_picture' => 'none',
                'ntf3_text' => '',
                'ntf3_link' => '',
                'ntf3_count' => '',
                'ntf3_date' => '',
                'ntf3_icon' => '',
                //NTF4
                'ntf4_display' => 'none',
                'ntf4_picture' => '',
                'ntf4_display_picture' => 'none',
                'ntf4_text' => '',
                'ntf4_link' => '',
                'ntf4_count' => '',
                'ntf4_date' => '',
                'ntf4_icon' => '',
                //NTF5
                'ntf5_display' => 'none',
                'ntf5_picture' => '',
                'ntf5_display_picture' => 'none',
                'ntf5_text' => '',
                'ntf5_link' => '',
                'ntf5_count' => '',
                'ntf5_date' => '',
                'ntf5_icon' => '',
            ];
            $idx = 0;
            $important = 0;
            $date = (new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d\TH:i:s\Z');
            foreach($events as $event){
                if($idx === 0){
                    $labels['ntf_picture'] =  (null !== $event['picture']) ? ($urldms.$event['picture'].'-80m80') : null;
                    $labels['ntf_display_picture'] =  (null !== $event['picture']) ? 'block' : 'none';
                    $labels['ntf_text'] = $event['text'];
                    $labels['title'] = strip_tags(html_entity_decode($event['text']));
                    $labels['ntf_count'] = $event['count']  > 1 ? sprintf('And <b>%s</b> more...', $event['count']) : '';
                    $labels['ntf_link'] =  sprintf('https://%s.%s%s',$libelle, $urlui, $this->getLink($event['event'],json_decode($event['object'], true)));
                    $labels['unsubscribe'] =  sprintf('https://%s.%s/unsubscribe/%s',$libelle, $urlui, md5($uid.$event['id'].$event['date'].$event['object']));
                    $idx++;
                    $important = $event['important'];
                }
                else if($idx < 6){
                      $labels['ntf'.$idx.'_display'] = 'block';
                      $labels['ntf'.$idx.'_picture'] = (null !== $event['picture']) ? ($urldms.$event['picture'].'-80m80') : null;
                      $labels['ntf'.$idx.'_display_picture'] =  (null !== $event['picture']) ? 'block' : 'none';
                      $labels['ntf'.$idx.'_text'] = $event['text'];
                      $labels['ntf'.$idx.'_count'] = $event['count']  > 1 ? sprintf('And <b>%s</b> more...', $event['count']) : '';
                      $labels['ntf'.$idx.'_icon'] = '/assets/img/mail/'.$event['event'].'.png';
                      $labels['ntf'.$idx.'_date'] = $event['date'];
                      $labels['ntf'.$idx.'_link'] =  sprintf('https://%s.%s%s',$libelle, $urlui, $this->getLink($event['event'],json_decode($event['object'], true)));
                      $idx++;
                }
            }
            if(($important === 0 && $m_user->getHasSocialNotifier() === 1) ||
               ($important === 1 && $m_user->getHasAcademicNotifier() === 1)){
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

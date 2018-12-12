<?php
/**
 * TheStudnet (http://thestudnet.com)
 *
 * Post
 */
namespace Application\Service;

use Application\Model\Page as ModelPage;
use Dal\Service\AbstractService;
use Application\Model\Role as ModelRole;
use Application\Model\PostSubscription as ModelPostSubscription;
use Zend\Db\Sql\Predicate\IsNull;
use Zend\Http\Client;
use ZendService\Google\Gcm\Notification as GcmNotification;
use JRpc\Json\Server\Exception\JrpcException;

/**
 * Class Post
 */
class Post extends AbstractService
{

    public function isMine($id)
    {
        $m_post = $this->getLite($id);
        $identity = $this->getServiceUser()->getIdentity();
        return $m_post->getUserId() === $identity['id'];
    }

    /**
     * Add Post
     *
     * @invokable
     *
     * @param string $content
     * @param string $picture
     * @param string $name_picture
     * @param string $link
     * @param string $link_title
     * @param string $link_desc
     * @param int    $parent_id
     * @param int    $t_page_id
     * @param int    $t_user_id
     * @param int    $page_id
     * @param int    $lat
     * @param int    $lng
     * @param array  $docs
     * @param string $data
     * @param string $event
     * @param string $uid
     * @param array  $sub
     * @param string $type
     * @param int    $item_id
     * @param int    $shared_id
     *
     * @return \Application\Model\Post
     */
    public function add(
        $content = null,
        $picture = null,
        $name_picture = null,
        $link = null,
        $link_title = null,
        $link_desc = null,
        $parent_id = null,
        $t_page_id = null,
        $t_user_id = null,
        $page_id = null,
        $lat =null,
        $lng = null,
        $docs = null,
        $data = null,
        $event = null,
        $uid = null,
        $sub = null,
        $type = null,
        $item_id = null,
        $shared_id = null
    ) {
          return $this->_add($content, $picture,  $name_picture, $link, $link_title, $link_desc, $parent_id,$t_page_id, $t_user_id, $page_id, $lat, $lng, $docs, $data, $event, $uid, $sub, $type, $item_id, $shared_id);
    }

    /**
     * Add Post
     *
     *
     * @param string $content
     * @param string $picture
     * @param string $name_picture
     * @param string $link
     * @param string $link_title
     * @param string $link_desc
     * @param int    $parent_id
     * @param int    $t_page_id
     * @param int    $t_user_id
     * @param int    $page_id
     * @param int    $lat
     * @param int    $lng
     * @param array  $docs
     * @param string $data
     * @param string $event
     * @param string $uid
     * @param array  $sub
     * @param string $type
     * @param int    $item_id
     * @param int    $shared_id
     * @param array  $notify
     *
     * @return \Application\Model\Post
     */
    public function _add(
        $content = null,
        $picture = null,
        $name_picture = null,
        $link = null,
        $link_title = null,
        $link_desc = null,
        $parent_id = null,
        $t_page_id = null,
        $t_user_id = null,
        $page_id = null,
        $lat =null,
        $lng = null,
        $docs = null,
        $data = null,
        $event = null,
        $uid = null,
        $sub = null,
        $type = null,
        $item_id = null,
        $shared_id = null,
        $notify = null
    ) {
        $user_id = $this->getServiceUser()->getIdentity()['id'];
        $origin_id = null;
        if (null !== $parent_id) {
            $m_post = $this->getMapper()->select($this->getModel()->setId($parent_id))->current();
            $origin_id = (is_numeric($m_post->getOriginId())) ?
                $m_post->getOriginId()  :
                $m_post->getId();
            $uid = $m_post->getUid();
        }

        if (empty($type)) {
            $type = 'post';
        }
        $uid = (($uid) && is_string($uid) && !empty($uid)) ? $uid:null;
        $is_notif = !!$uid;
        $date = (new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s');
        if (!$is_notif && null === $parent_id && null === $t_page_id && null === $t_user_id) {
            $t_user_id = $user_id;
        }

        if (null !== $parent_id) {
            $uid = null;
        }

        $m_post = $this->getModel()
            ->setContent($content)
            ->setPicture($picture)
            ->setNamePicture($name_picture)
            ->setLink($link)
            ->setLinkTitle($link_title)
            ->setLinkDesc($link_desc)
            ->setCreatedDate((new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s'))
            ->setPageId($page_id)
            ->setLat($lat)
            ->setItemId($item_id)
            ->setLng($lng)
            ->setParentId($parent_id)
            ->setOriginId($origin_id)
            ->setTPageId($t_page_id)
            ->setTUserId($t_user_id)
            ->setUid($uid)
            ->setType($type)
            ->setData(!empty($data) && !is_string($data) ? json_encode($data) : $data)
            ->setSharedId($shared_id);

        if (!$is_notif || null !== $parent_id) {
            $m_post->setUserId($user_id);
        }

        if ($this->getMapper()->insert($m_post) <= 0) {
            throw new \Exception('error add post');// @codeCoverageIgnore
        }
        $id = $this->getMapper()->getLastInsertValue();

        $d = ['id' => (int)$id, 'parent_id' => $parent_id, 'origin_id' => $origin_id];
        if (is_array($data)) {
            $data = array_merge($d, $data);
        } else {
            $data = $d;
        }

        if (null !== $docs) {
            $this->getServicePostDoc()->_add($id, $docs);
        }

        $base_id = ($origin_id !== null && $shared_id === null) ? $origin_id:$id;
        $m_post_base = $this->getLite($base_id);
        $is_not_public_page = (is_numeric($m_post_base->getTPageId()) && ($this->getServicePage()->getLite($m_post_base->getTPageId())->getConfidentiality() !== ModelPage::CONFIDENTIALITY_PUBLIC));
        $pevent = [];
        $et = $this->getTarget($m_post_base);
        // S'IL Y A UNE CIBLE A LA BASE ET que l'on a pas definie d'abonnement ON NOTIFIE  P{target}nbr
        if (false !== $et && empty($sub) /*&& null === $parent_id*/) {
            $pevent = array_merge($pevent, ['P'.$et]);
        }

        if (!$is_notif) {
            $pevent = array_merge($pevent, ['P'.$this->getOwner($m_post_base),'M'.$m_post_base->getUserId()]);
        }

        if ($parent_id && $origin_id) {
            // SI N'EST PAS PRIVATE ET QUE CE N'EST PAS UNE NOTIF -> ON NOTIFIE LES AMIES DES OWNER
            $m_post = $this->getLite($id);
            if (!$is_notif && null === $page_id) {
                $pevent = array_merge($pevent, ['P'.$this->getOwner($m_post)]);
            }
            if(!$m_post_base->getUserId() instanceof IsNull&& null === $page_id){
                $pevent = array_merge($pevent, ['M'.$m_post_base->getUserId()]);
            }
            // SI NOTIF ET QUE LE PARENT N'A PAS DE TARGET ON RECUPERE TTES LES SUBSCRIPTIONS
            if ($is_notif && null === $sub && $et === false) {
                $sub = $this->getServicePostSubscription()->getListLibelle($origin_id);
            }
        }
        if($notify === null ) {
            $notify = ['fcm' => Fcm::PACKAGE_TWIC_APP, 'mail' => false];
            if($parent_id == null && null === $item_id && !$is_notif && $t_page_id != null && $this->getServicePage()->isAdmin($t_page_id)) {
                $m_page = $this->getServicePage()->getLite($t_page_id);
                if($m_page->getType() == ModelPage::TYPE_COURSE && $type === 'post' && $m_page->getIsPublished()) {
                    $notify['mail'] = true;
                }
                else if($m_page->getType() == ModelPage::TYPE_COURSE && $type === 'post' && !$m_page->getIsPublished()){
                    $notify = false;
                }
            }
        }
        if (!empty($sub)) {
            $pevent = array_merge($pevent, $sub);
        }
        if(null !== $shared_id){
            $ev = ModelPostSubscription::ACTION_SHARE;
        }
        else{
            $ev=((!empty($event))? $event:(($base_id!==$id) ? ModelPostSubscription::ACTION_COM : ModelPostSubscription ::ACTION_CREATE));
        }

          // si c pas une notification on gére les hastags
        $mentions = [];
        if (!$is_notif) {
            preg_match_all ( '/@{user:(\d+)}/', $content, $mentions );
            if(count($mentions[0]) > 0){
                $ar_users = $this->getServiceHashtag()->addMentions($id, $mentions);
                if($is_not_public_page){
                    $ar_buffer = [];
                    $ar_subscribers = $this->getServicePage()->getListSuscribersId($m_post_base->getTPageId());
                    foreach($ar_users as $user_id){
                        if(in_array($user_id, $ar_subscribers)){
                            $ar_buffer[] = $user_id;
                        }
                    }
                    $ar_users = $ar_buffer;
                }
                if(count($ar_users) > 0){
                    $date = (new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s');
                    foreach($ar_users as $uid){
                        $this->getServicePostSubscription()->add(
                            'M'.$uid,
                            $id,
                            $date,
                            ModelPostSubscription::ACTION_TAG,
                            $user_id,
                            (($base_id!==$id) ? $id:null),
                            $data,
                            $is_not_public_page,
                            $notify !== false ? ['fcm' => $notify['fcm'], 'mail' => 0 ] : false
                        );
                    }
                }
            }
        }

        if(count($pevent) > 0){
            $this->getServicePostSubscription()->add(
                array_unique($pevent),
                $base_id,
                $date,
                $ev,
                ((!$is_notif) ? $user_id:null),
                ($base_id !== $id ? $id : null),
                $data,
                $is_not_public_page,
                $notify
            );
        }



        return $id;
    }

    /**
     * Update Post
     *
     * @invokable
     *
     * @param int     $id
     * @param string  $content
     * @param string  $link
     * @param string  $picture
     * @param string  $name_picture
     * @param string  $link_title
     * @param string  $link_desc
     * @param int     $lat
     * @param int     $lng
     * @param array   $docs
     * @param string  $data
     * @param string  $event
     * @param int     $uid
     * @param array   $sub
     * @param $item_id
     *
     * @return \Application\Model\Post
     */
    public function update(
        $id = null,
        $content = null,
        $link = null,
        $picture = null,
        $name_picture = null,
        $link_title = null,
        $link_desc = null,
        $lat = null,
        $lng = null,
        $docs =null,
        $data = null,
        $event = null,
        $uid = null,
        $sub = null,
        $item_id = null
    ) {
        if ($uid === null && $id === null) {
            throw new \Exception('error update: no $id and no $uid');
        }

        if (!$this->getServiceUser()->isStudnetAdmin() && !$this->isMine($id)) {
            throw new JrpcException('Unauthorized operation post.update', -38003);
        }


        return $this->_update(
            $id,
            $content,
            $link,
            $picture,
            $name_picture,
            $link_title,
            $link_desc,
            $lat,
            $lng,
            $docs,
            $data,
            $event,
            $uid,
            $sub,
            $item_id
        );
    }

    public function _update(
        $id = null,
        $content = null,
        $link = null,
        $picture = null,
        $name_picture = null,
        $link_title = null,
        $link_desc = null,
        $lat = null,
        $lng = null,
        $docs =null,
        $data = null,
        $event = null,
        $uid = null,
        $sub = null,
        $item_id = null,
        $replace_sub = null,
        $notify = null
    ) {
        $user_id = $this->getServiceUser()->getIdentity()['id'];
        $date = (new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s');

        //recup id de base
        $m_post_base = ($uid !== null && $id === null) ? $this->getLite(null, $uid) : $this->getLite($id);
        $id = $m_post_base->getId();

        // check if notif
        $uid = (is_string($uid) && !empty($uid)) ? $uid:false;
        $event = (is_string($event) && !empty($event)) ? $event:false;
        $is_notif = ($uid && $event);

        // create where request
        $w = ($uid !== false) ?  ['id' => $id] : ['id' => $id, 'user_id' => $user_id];
        $d = ['id' => (int)$id ];
        if (is_array($data)) {
            $data = array_merge($d, $data);
        } else {
            $data = $d;
        }

        $m_post = $this->getModel()
            ->setContent($content)
            ->setLink(($link==='')?new IsNull():$link)
            ->setPicture(($picture==='')?new IsNull():$picture)
            ->setNamePicture(($name_picture==='')?new IsNull():$name_picture)
            ->setLinkTitle(($link_title==='')?new IsNull():$link_title)
            ->setLinkDesc(($link_desc==='')?new IsNull():$link_desc)
            ->setLat($lat)
            ->setLng($lng)
            ->setItemId($item_id)
            ->setData(!empty($data) && !is_string($data) ? json_encode($data) : $data)
            ->setUpdatedDate($date);

        if (null !== $docs) {
            $this->getServicePostDoc()->replace($id, $docs);
        }

        $ret = $this->getMapper()->update($m_post, $w);
        if ($ret > 0) {
            $is_not_public_page = (is_numeric($m_post_base->getTPageId()) && ($this->getServicePage()->getLite($m_post_base->getTPageId())->getConfidentiality() !== ModelPage::CONFIDENTIALITY_PUBLIC));

           // si c pas une notification on gére les hastags

            $pevent = [];

            // if ce n'est pas un page privée
            if (!$is_notif) {
                $pevent = array_merge($pevent, ['P'.$this->getOwner($m_post_base)]);
                // S'IL Y A UNE CIBLE A LA BASE ON NOTIFIE
                $et = $this->getTarget($m_post_base);
                if (false !== $et) {
                    $pevent = array_merge($pevent, ['P'.$et]);
                }
            }
            if (!empty($sub)) {
                $pevent = array_merge($pevent, $sub);
            }
            if(true === $replace_sub){
                $this->getServicePostSubscription()->delete($id);
            }
            $this->getServicePostSubscription()->add(
                array_unique($pevent),
                $id,
                $date,
                (!empty($event)? $event:ModelPostSubscription::ACTION_UPDATE),
                $user_id,
                null,
                $data,
                $is_not_public_page,
                $notify
            );

            if (!$is_notif) {

                $mentions = [];
                preg_match_all ( '/@{user:(\d+)}/', $content, $mentions );
                if(count($mentions[0]) > 0){
                    $ar_users = $this->getServiceHashtag()->addMentions($id, $mentions);
                    if($is_not_public_page){
                        $ar_buffer = [];
                        $ar_subscribers = $this->getServicePage()->getListSuscribersId($m_post_base->getTPageId());
                        foreach($ar_users as $user_id){
                            if(in_array($user_id, $ar_subscribers)){
                                $ar_buffer[] = $user_id;
                            }
                        }
                        $ar_users = $ar_buffer;
                    }
                    if(count($ar_users) > 0){
                        $date = (new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s');
                        foreach($ar_users as $uid){
                            $this->getServicePostSubscription()->add(
                                'M'.$uid,
                                $id,
                                $date,
                                ModelPostSubscription::ACTION_TAG,
                                $user_id,
                                null,
                                null,
                                $is_not_public_page,
                                $notify
                            );
                        }
                    }
                }

            }

        }

        return $ret;
    }

    /**
     * Get Post
     *
     * @invokable
     *
     * @param  int $id
     * @return \Application\Model\Post
     */
    public function get($id)
    {
        $identity = $this->getServiceUser()->getIdentity();
        $is_sadmin = $identity && (in_array(ModelRole::ROLE_ADMIN_STR, $identity['roles']));
        $res_post = $this->getMapper()->get($identity['id'], $id, $is_sadmin);
        $ids = [];
        foreach ($res_post as $m_post) {
            $m_post->setDocs($this->getServicePostDoc()->getList($m_post->getId()));
            $m_post->setSubscription($this->getServicePostSubscription()->getLastLite($m_post->getId()));
            $m_post->setMentions($this->getServiceHashtag()->getListMentions($m_post->getId()));
            if (is_string($m_post->getData())) {
                $m_post->setData(json_decode($m_post->getData(), true));
            }
            $ids[] = $m_post->getId();
        }

        $this->getServicePostUser()->view($ids);
        $res_post->rewind();

        if (is_array($id)) {
            $ar_post = $res_post->toArray(['id']);
            foreach ($id as $i) {
                if (!isset($ar_post[$i])) {
                    $ar_post[$i] = null;
                }
            }
        }

        return (is_array($id) ? $ar_post: $res_post->current());
    }

    /**
     * Get List Post
     *
     * @invokable
     *
     * @param array $filter
     * @param int   $user_id
     * @param int   $page_id
     * @param int   $parent_id
     * @param bool  $is_item
     * @param string  $type
     */
    public function getListId($filter = null, $user_id = null, $page_id = null, $parent_id = null, $is_item = null, $type = null)
    {
        $identity = $this->getServiceUser()->getIdentity();
        $mapper = (null !== $filter) ?
            $this->getMapper()->usePaginator($filter) :
            $this->getMapper();

        $res_posts = $mapper->getListId($identity['id'], $page_id, $user_id, $parent_id, $is_item, (in_array(ModelRole::ROLE_ADMIN_STR, $identity['roles'])), $type);

        return (null !== $filter) ?
            ['count' => $mapper->count(), 'list' => $res_posts] :
            $res_posts;
    }

    /**
     * Hide post
     *
     * @invokable
     *
     * @param int $id
     */
    public function hide($id)
    {
        return $this->getServicePostUser()->hide($id);
    }

    /**
     * Like post
     *
     * @invokable
     *
     * @param int $id
     */
    public function like($id)
    {

        return $this->getServicePostLike()->add($id);
    }

    /**
     * UnLike Post
     *
     * @invokable
     *
     * @param int $id
     */
    public function unlike($id)
    {

        return $this->getServicePostLike()->delete($id);
    }

    /**
     * Delete Post
     *
     * @invokable
     *
     * @param  int $id
     * @return int
     */
    public function delete($id)
    {
        if (!$this->getServiceUser()->isStudnetAdmin() && !$this->isMine($id)) {
            throw new JrpcException('Unauthorized operation post.delete', -38003);
        }
        $identity = $this->getServiceUser()->getIdentity();
        $is_sadmin = (in_array(ModelRole::ROLE_ADMIN_STR, $identity['roles']));
        $m_post = $this->getModel()->setDeletedDate((new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s'));



        $ret =  (!$is_sadmin) ?
          $this->getMapper()->update($m_post, ['id' => $id, 'user_id' => $identity['id']]) :
          $this->getMapper()->update($m_post, ['id' => $id]);

        if ($ret) {
            $this->getServiceEvent()->sendData($id, 'post.delete', ['PU'.$this->getLite($id)->getUserId()]);
        }

        return $ret;
    }

    /**
     * Reactivate Post
     *
     * @invokable
     *
     * @param  int $id
     * @return int
     */
    public function reactivate($id)
    {
        //$this->deleteSubscription($id);

        $m_post = $this->getModel()->setDeletedDate(new \Zend\Db\Sql\Predicate\IsNull());

        return $this->getMapper()->update($m_post, ['id' => $id]);
    }

    /**
     * hard Delete
     */
    public function hardDelete($uid)
    {
        return (is_string($uid) && !empty($uid)) ?  $this->getMapper()->delete($this->getModel()->setUid($uid)) : false;
    }

    /**
     * Get Post Lite
     *
     * @param  int $id
     * @param  int $uid
     * @param  int $item_id
     * @return \Application\Model\Post
     */
    public function getLite($id = null, $uid = null, $item_id = null)
    {
        $res_post = $this->getMapper()->select($this->getModel()->setId($id)->setUid($uid)->setItemId($item_id));
        if(is_array($id)){
            $ar_post = [];
            foreach($res_post as $m_post){
                $ar_post[$m_post->getId()] = $m_post;
            }
            return $ar_post;
        }
        return $res_post->current();
    }

    public function getOwner($m_post)
    {
        switch (true) {
        case (is_numeric($m_post->getPageId())):
            $u = 'P'.$m_post->getPageId();
            break;
        case (is_numeric($m_post->getUserId())):
            $u ='U'.$m_post->getUserId();
            break;
        default:
            $u = null;
            break;
        }

        return $u;
    }

    public function getTarget($m_post)
    {
        switch (true) {
        case (is_numeric($m_post->getTUserId())):
            $t = 'U'.$m_post->getTUserId();
        break;
        case (is_numeric($m_post->getTPageId())):
            $t = 'P'.$m_post->getTPageId();
            break;
        default:
            $t = false;
            break;
        }

        return $t;
    }

    /**
     * Add Sys
     *
     * @param string $uid
     * @param string $content
     * @param string $data
     * @param string $event
     * @param string $sub
     * @param int    $parent_id
     * @param int    $t_page_id
     * @param int    $t_user_id
     * @param string $type
     * @param int    $page_id
     * @param int    $item_id
     * @param bool    $replace_sub
     * @param array    $notify
     *
     * @return \Application\Model\Post
     */
    public function addSys($uid, $content, $data, $event, $sub = null, $parent_id = null, $t_page_id = null, $t_user_id = null, $type = null, $page_id = null, $item_id = null, $replace_sub = null, $notify = null)
    {
        $res_post = $this->getMapper()->select($this->getModel()->setUid($uid));
        if($res_post->count() > 0){
            $this->getServicePostUser()->show(null, $uid);
            return $this->_update(null, $content, null, null, null, null, null, null, null, null, $data, $event, $uid, $sub, $item_id,  $replace_sub, $notify);
        }
        else{
           return   $this->_add(
                 $content,
                 null,
                 null,
                 null,
                 null,
                 null,
                 $parent_id,
                 $t_page_id,
                 $t_user_id,
                 $page_id,
                 null,
                 null,
                 null,
                 $data,
                 $event,
                 $uid,
                 $sub,
                 $type,
                 null,
                 null,
                 $notify
             );
        }
    }

    /**
     * updateSys
     *
     * @param  string $uid
     * @param  string $content
     * @param  string $data
     * @param  string $event
     * @param  array  $sub
     * @param  array  $notify
     * @return int
     */
    public function updateSys($uid, $content, $data, $event, $sub = null, $notify = null)
    {
       return $this->_update(null, $content, null, null, null, null, null, null, null, null, $data, $event, $uid, $sub, null, null, $notify);
    }

    /**
     * Get preview Crawler.
     *
     * @invokable
     *
     * @param string $url
     *
     * @return array
     */
    public function linkPreview($url)
    {
        $client = new Client();
        $client->setOptions($this->container->get('Config')['http-adapter-curl']);

        $pc = $this->getServiceSimplePageCrawler();
        $page = $pc->setHttpClient($client)->get($url);

        $return = $page->getMeta()->toArray();
        $return['images'] = $page->getImages()->getImages();
        if (isset($return['meta'])) {
            foreach ($return['meta'] as &$v) {
                $v = html_entity_decode(html_entity_decode($v));
            }
        }
        if (isset($return['open_graph'])) {
            foreach ($return['open_graph'] as &$v) {
                $v = html_entity_decode(html_entity_decode($v));
            }
        }

        return $return;
    }

     /**
      * Get page counts.
      *
      * @invokable
      *
      * @param string $start_date
      * @param string $end_date
      * @param string $interval_date
      * @param int $parent
      * @param int|array $page_id
      * @param int $date_offset
      *
      * @return array
      */
    public function getCount( $start_date = null, $end_date = null, $interval_date = 'D', $parent = null, $page_id  = null, $date_offset = 0)
    {

        if(null !== $page_id && !is_array($page_id)){
            $page_id = [$page_id];
        }
        $interval = $this->getServiceActivity()->interval($interval_date);
        $identity = $this->getServiceUser()->getIdentity();

        return $this->getMapper()->getCount($identity['id'], $interval, $start_date, $end_date, $page_id, $parent, $date_offset);
    }

    public function getPostInfos($id){
        return $this->getMapper()->getPostInfos($id)->current()->toArray();
    }


  public function addFromApi($apikey, $content = null, $link = null, $page_id = null){
        $m_user = $this->getServiceUser()->getLiteByApiKey($apikey);
        if(false !== $m_user && (null === $page_id || $this->getServicePage()->isAdmin($page_id, $m_user->getId()))){
            $link_title = null;
            $link_desc = null;
            $picture = null;
            if(null !== $link){
                $meta = $this->linkPreview($link);
                if( isset($meta['meta'])){
                    if(isset($meta['meta']['title'])){
                        $link_title = $meta['meta']['title'];
                    }
                    elseif(isset($meta['meta']['twitter:title'])){
                        $link_title = $meta['meta']['twitter:title'];
                    }
                    if(isset($meta['meta']['description'])){
                        $link_title = $meta['meta']['description'];
                    }
                    elseif(isset($meta['meta']['twitter:description'])){
                        $link_title = $meta['meta']['twitter:description'];
                    }
                    if(isset($meta['meta']['image'])){
                        $link_title = $meta['meta']['image'];
                    }
                    elseif(isset($meta['meta']['twitter:image'])){
                        $link_title = $meta['meta']['twitter:image'];
                    }
                }
                if( isset($meta['open_graph'])){
                    if(isset($meta['open_graph']['title'])){
                        $link_title = $meta['open_graph']['title'];
                    }
                    elseif(isset($meta['open_graph']['site_name'])){
                        $link_title = $meta['open_graph']['site_name'];
                    }
                    if(isset($meta['open_graph']['description'])){
                        $link_desc = $meta['open_graph']['description'];
                    }
                    if(isset($meta['open_graph']['image'])){
                        $picture = $meta['open_graph']['image'];
                    }
                }

                if( null == $picture && isset($meta['images']) && count($meta['images']) > 0){
                    $picture = $meta['images'][0];
                }
            }
            return $this->add($content, $picture, null, $link, $link_title, $link_desc, null,
                  $page_id, (null !== $page_id ? $m_user->getId() : null), $page_id, null,
                  null, null, null, null, null, null, 'post');
        }
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
     * Get Service Post Doc
     *
     * @return \Application\Service\PostDoc
     */
    private function getServicePostDoc()
    {
        return $this->container->get('app_service_post_doc');
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
     * @return \Application\Service\PostLike
     */
    private function getServicePostLike()
    {
        return $this->container->get('app_service_post_like');
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
     * Get Service Post Like
     *
     * @return \Application\Service\Hashtag
     */
    private function getServiceHashtag()
    {
        return $this->container->get('app_service_hashtag');
    }

    /**
     * Get Service Event
     *
     * @return \Application\Service\Event
     */
    private function getServiceEvent()
    {
        return $this->container->get('app_service_event');
    }

    /**
     * Get Service PageCrawler.
     *
     * @return \SimplePageCrawler\PageCrawler
     */
    private function getServiceSimplePageCrawler()
    {
        return $this->container->get('SimplePageCrawler');
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
     * Get Service Activity
     *
     * @return \Application\Service\Activity
     */
    private function getServiceActivity()
    {
        return $this->container->get('app_service_activity');
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
     * Get Service Service Post User.
     *
     * @return \Application\Service\PostUser
     */
    private function getServicePostUser()
    {
        return $this->container->get('app_service_post_user');
    }
}

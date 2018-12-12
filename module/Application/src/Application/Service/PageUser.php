<?php
/**
 * TheStudnet (http://thestudnet.com).
 *
 * Page User
 */
namespace Application\Service;

use Application\Model\Page as ModelPage;
use Application\Model\PageRelation as ModelPageRelation;
use Application\Model\PageUser as ModelPageUser;
use Application\Model\Role as ModelRole;
use Dal\Service\AbstractService;
use Exception;
use JRpc\Json\Server\Exception\JrpcException;
use Mail\Service\Mail;
use Zend\Db\Sql\Predicate\IsNull;
use ZendService\Google\Gcm\Notification as GcmNotification;


/**
 * Class PageUser
 *
 * @TODO Check vérification sécuriter des user page + parmas par default role use + exposer méthode spécifique pour l'acceptation
 */
class PageUser extends AbstractService
{

    /**
     * Add Page User Relation
     *
     * @invokable
     *
     * @param  int       $page_id
     * @param  int|array $user_id
     * @param  string    $role
     * @param  string    $state
     * @param  string    $email
     * @param  bool       $is_pinned
     * @return int
     */
    public function add($page_id, $user_id, $role, $state, $email = null, $is_pinned = 0)
    {
        $identity = $this->getServiceUser()->getIdentity();
        if(!$this->getServiceUser()->isStudnetAdmin()
            && !$this->getServicePage()->isAdmin($page_id)
            && $identity['id'] != $user_id) {
            throw new JrpcException('Unauthorized operation pageuser.add', -38003);
        }

        return $this->_add($page_id, $user_id, $role, $state, $email, $is_pinned);
    }


    public function _add($page_id, $user_id, $role, $state, $email = null, $is_pinned = 0)
    {

        if (!is_array($user_id)) {
            $user_id = [$user_id];
        }

        if(null !== $email) {
            if (!is_array($email)) {
                $email = [$email];
            }
            foreach($email as $e){
                $id = $this->getServiceUser()->_add(null, null, $e, null, null, null, null, null, null, null, null);
                $user_id[] = $id;
            }
        }

        $m_page_user = $this->getModel()
            ->setPageId($page_id)
            ->setRole($role)
            ->setState($state)
            ->setIsPinned($is_pinned)
            ->setCreatedDate((new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s'));
        $ret = 0;

        $m_page = $this->getServicePage()->getLite($page_id);
        // ON MET LES USER DANS LA CONVERSATION SI ELLE EXISTE
        if ($state === ModelPageUser::STATE_MEMBER) {
            if (is_numeric($m_page->getConversationId())) {
                $this->getServiceConversationUser()->add($m_page->getConversationId(), $user_id);
            }
        }

        foreach ($user_id as $uid) {
            $ret +=  $this->getMapper()->insert($m_page_user->setUserId($uid));
            $identity = $this->getServiceUser()->getIdentity();
            $is_admin = $this->getServiceUser()->isStudnetAdmin();
            $m_user = $this->getServiceUser()->getLite($uid);
            if (($state === ModelPageUser::STATE_MEMBER || $state === ModelPageUser::STATE_INVITED)
                    && ModelPage::TYPE_ORGANIZATION === $m_page->getType() && $m_user->getOrganizationId() instanceof IsNull) {
                $this->getServiceUser()->_update($uid, null, null, null, null, null, null, null, null, null, $page_id);
            }
            if ($state === ModelPageUser::STATE_PENDING && ModelPage::TYPE_ORGANIZATION !== $m_page->getType()) {
                $arr_user = $this->getListByPage($page_id, ModelPageUser::ROLE_ADMIN)[$page_id];
                $sub = [];
                foreach($arr_user as $user){
                    $sub[] = 'M'.$user;
                }

                $this->getServiceEvent()->create('page', 'pending',
                     null,
                      $sub,
                      [
                          'state' => 'pending',
                          'user'  => $uid,
                          'page'  => $page_id,
                          'page_type' => $m_page->getType(),
                          'picture' => !($m_user->getAvatar() instanceof IsNull) ? $m_user->getAvatar() : null
                      ],
                      [
                        'source' => $m_user->getFirstname().' '.$m_user->getLastname(),
                        'page_type' => $m_page->getType(),
                        'page_title' => $m_page->getTitle()
                      ],   ['fcm' => Fcm::PACKAGE_TWIC_APP, 'mail' => false] );
            }
            if ($state === ModelPageUser::STATE_INVITED && ModelPage::TYPE_ORGANIZATION !== $m_page->getType()) {

                $this->getServiceEvent()->create('page', 'invited',
                     null,
                      ['M'.$uid],
                      [
                        'state' => 'invited',
                        'user'  => $uid,
                        'page'  => $page_id,
                        'target' => $uid,
                        'page_type' => $m_page->getType(),
                        'picture' => !($m_page->getLogo() instanceof IsNull) ? $m_page->getLogo() : null
                      ],
                      [
                        'page_type' => $m_page->getType(),
                        'page_title' => $m_page->getTitle()
                      ],   ['fcm' => Fcm::PACKAGE_TWIC_APP, 'mail' => false] );
                // member only group
            } elseif ($state === ModelPageUser::STATE_MEMBER) {


                $this->getServiceSubscription()->add('PP'.$page_id, $uid);
                if(ModelPage::TYPE_ORGANIZATION == $m_page->getType()) {
                    $res_page_relation = $this->getServicePageRelation()->getList($page_id, ModelPageRelation::TYPE_MEMBER);
                    foreach ($res_page_relation as $m_page_relation) {
                        $this->getServiceSubscription()->add("PP".$m_page_relation->getParentId(), $uid);
                    }
                }
                //On envoi la notification qu'il vient d'être ajouté à un cours publié
                if(( $m_page->getType() === ModelPage::TYPE_COURSE && $m_page->getIsPublished() ) || $m_page->getType() !== ModelPage::TYPE_COURSE) {


                    $this->getServiceEvent()->create('page', 'member',
                         null,
                          ['M'.$uid],   [
                            'state' => 'member',
                            'user'  => $uid,
                            'page'  => $page_id,
                            'target' => $uid,
                            'page_type' => $m_page->getType(),
                            'picture' => !($m_page->getLogo() instanceof IsNull) ? $m_page->getLogo() : null
                          ],
                          [
                            'page_type' => $m_page->getType(),
                            'page_title' => $m_page->getTitle()
                          ],   ['fcm' => Fcm::PACKAGE_TWIC_APP, 'mail' => false] );

                }
            }
        }



        return $ret;
    }


    /**
     * Update Page User Relation
     *
     * @invokable
     *
     * @param  int    $page_id
     * @param  int    $user_id
     * @param  string $role
     * @param  string $state
     * @param  bool $is_pinned
     * @return int
     */
    public function update($page_id, $user_id, $role = null, $state = null, $is_pinned = null)
    {
        $m_page_user = $this->getMapper()->select($this->getModel()->setPageId($page_id)->setUserId($user_id))->current();
        // si on doit l'abonner
        if (ModelPageUser::STATE_MEMBER === $state) {

            // ON MET LES USER DANS LA CONVERSATION SI ELLE EXISTE
            $m_page = $this->getServicePage()->getLite($page_id);
            if (is_numeric($m_page->getConversationId())) {
                $this->getServiceConversationUser()->add($m_page->getConversationId(), $user_id);
            }

            if ($m_page_user->getState() === ModelPageUser::STATE_PENDING || $m_page_user->getState() === ModelPageUser::STATE_INVITED) {
                $this->getServiceSubscription()->add('PP'.$page_id, $user_id);
                if(ModelPage::TYPE_ORGANIZATION == $m_page->getType()) {
                    $res_page_relation = $this->getServicePageRelation()->getList($page_id, ModelPageRelation::TYPE_MEMBER);
                    foreach ($res_page_relation as $m_page_relation) {
                        $this->getServiceSubscription()->add("PP".$m_page_relation->getParentId(), $user_id);
                    }
                }

                $this->getServiceEvent()->sendData($page_id, 'page.member', ['M'.$user_id]);

            }
        }
        /*
                $this->getServiceFcm()->send(
                    $user_id, [
                    'data' => [
                        'type' => 'userpage',
                        'data' => [
                            'state' => $state,
                            'page' => $page_id,
                        ],
                    ],
                  ]
                );
        */

        //si on veux modifier le dernier administrateur
        if ($m_page_user->getRole() == 'admin' && $role !== 'admin' && $role !== null) {
            $ar_pu = $this->getListByPage($page_id, 'admin');
            if (count($ar_pu[$page_id]) === 1 && in_array($user_id, $ar_pu[$page_id])) {
                throw new Exception("Can't update the last administrator");
            }
        }

        $m_page_user = $this->getModel();
        if(null !== $role) {
            $m_page_user->setRole($role);
        }
        if(null !== $state) {
            $m_page_user->setState($state);
        }
        if(null !== $is_pinned) {
            $m_page_user->setIsPinned($is_pinned);
        }

        return $this->getMapper()->update($m_page_user, ['page_id' => $page_id, 'user_id' => $user_id]);
    }

    /**
     * Delete Page User Relation
     *
     * @invokable
     *
     * @param  int $page_id
     * @param  int $user_id
     * @return int
     */
    public function delete($page_id, $user_id)
    {
        $me = $this->getServiceUser()->getIdentity()['id'];
        if(!is_array($user_id)){
            $user_id = [$user_id];
        }
        $m_page_user = $this->getModel()
            ->setPageId($page_id)
            ->setUserId($user_id);

        //si on suprime le dernier administrateur
        $ar_pu = $this->getListByPage($page_id, 'admin');
        if (count($ar_pu[$page_id]) <= count($user_id)){
            $all_admin_removed = true;
            foreach($ar_pu[$page_id] as $u){
                $all_admin_removed &= in_array($u, $user_id);
            }
            if($all_admin_removed){
                throw new Exception("Can't delete last administrator");
            }
        }

        $ret =  $this->getMapper()->delete($m_page_user);
        if ($ret > 0) {
            $this->getServiceSubscription()->delete('PP'.$page_id, $user_id);
            $res_page_relation = $this->getServicePageRelation()->getList($page_id, ModelPageRelation::TYPE_MEMBER);
            foreach ($res_page_relation as $m_page_relation) {
                $this->getServiceSubscription()->delete("PP".$m_page_relation->getParentId(), $user_id);
            }
            // ON DELETE LES USER DANS LA CONVERSATION SI ELLE EXISTE
            $m_page = $this->getServicePage()->getLite($page_id);
            if (is_numeric($m_page->getConversationId())) {
                $this->getServiceConversationUser()->delete($m_page->getConversationId(), $user_id);
            }
            foreach($user_id as $u){
                $this->getServicePost()->hardDelete('PPM'.$page_id.'_'.$u);
                if($u === $me){
                    $arr_user = $this->getListByPage($page_id, ModelPageUser::ROLE_ADMIN)[$page_id];
                    $sub = [];
                    foreach($arr_user as $user){
                        $sub[] = 'M'.$user;
                    }
                    $this->getServiceEvent()->sendData($page_id, 'pageuser.delete', $sub);
                }
                else{
                    $this->getServiceEvent()->sendData($page_id, 'pageuser.delete', ['M'.$u]);
                }
            }
        }

        return $ret;
    }

    /**
     * Get List userId by Page
     *
     * @invokable
     *
     * @param int|array $page_id
     * @param string    $role
     * @param string    $state
     * @param bool    $sent
     * @param bool       $is_pinned
     * @param string    $search
     * @param array    $order
     * @param bool    $alumni
     */
    public function getListByPage($page_id, $role = null, $state = null,
        $sent = null, $is_pinned = null, $search = null, $order = null, $alumni = null)
    {

        $identity = $this->getServiceUser()->getIdentity();
        if (!is_array($page_id)) {
            $page_id = [$page_id];
        }

        $ret = [];
        foreach ($page_id as $page) {
            $ret[$page] = [];
        }

        $is_admin = null === $identity || (in_array(ModelRole::ROLE_ADMIN_STR, $identity['roles']));
        $res_page_user = $this->getMapper()->getList($page_id, null, $role, $state, null, $is_admin ? null : $identity['id'], $sent, $is_pinned, $search, $order, $alumni);
        foreach ($res_page_user as $m_page_user) {
            $ret[$m_page_user->getPageId()][] = $m_page_user->getUserId();
        }

        return $ret;
    }

    /**
     * Get List pageId by User
     *
     * @invokable
     *
     * @param int|array $user_id
     * @param string    $role
     * @param string    $state
     * @param string    $type
     */
    public function getListByUser($user_id, $role = null, $state = null, $type = null)
    {
        $identity = $this->getServiceUser()->getIdentity();
        if (!is_array($user_id)) {
            $user_id = [$user_id];
        }

        $ret = [];
        foreach ($user_id as $user) {
            $ret[$user] = [];
        }

        $res_page_user = $this->getMapper()->getList(null, $user_id, $role, $state, $type, $identity['id'], null, null, null, ['type' => 'admin']);
        foreach ($res_page_user as $m_page_user) {
            $ret[$m_page_user->getUserId()][] = $m_page_user->getPageId();
        }

        return $ret;
    }

    public function getRole($page_id)
    {
        $identity = $this->getServiceUser()->getIdentity();

        return $this->getMapper()->getList($page_id, $identity['id'])->current();
    }

    /**
     * Add Array
     *
     * @param  int   $page_id
     * @param  array $data
     * @return array
     */
    public function addFromArray($page_id, $data)
    {
        $ret = [];
        foreach ($data as $ar_u) {
            $user_id = (isset($ar_u['user_id'])) ? $ar_u['user_id']:null;
            $role = (isset($ar_u['role'])) ? $ar_u['role']:null;
            $state = (isset($ar_u['state'])) ? $ar_u['state']:null;

            $ret[$user_id] = $this->_add($page_id, $user_id, $role, $state);
        }

        return $ret;
    }

    /**
     * Add Array
     *
     * @param  int   $page_id
     * @param  array $data
     * @return array
     */
    public function replace($page_id, $data)
    {
        $this->getMapper()->delete($this->getModel()->setPageId($page_id));

        return $this->addFromArray($page_id, $data);
    }

      /**
     * Get page user created dates for a page
     *
     * @invokable
     *
     * @param $page_id int
     * @param $user_id int|array
     */
    public function getCreatedDates($page_id, $user_id = null)
    {
        if(!$this->getServicePage()->isAdmin($page_id)) {
            throw new JrpcException('Unauthorized operation pageuser.getCreatedDates', -38003);
        }
        $res = [];
        if(null !== $user_id && !is_array($user_id)){
            $user_id = [$user_id];
            foreach($id as $i){
                $res[$i] = null;
            }
        }
        $res_page_user = $this->getMapper()->get($page_id, $user_id);
        foreach($res_page_user as $m_page_user){
            if(!$m_page_user->getCreatedDate() instanceof IsNull){
                $res[$m_page_user->getUserId()] = $m_page_user->getCreatedDate();
            }
        }
        return $res;
    }

    /**
     * Get Service Subscription
     *
     * @return Subscription
     */
    private function getServiceSubscription()
    {
        return $this->container->get('app_service_subscription');
    }



    /**
     * Get Service Event
     *
     * @return Event
     */
    private function getServiceEvent()
    {
        return $this->container->get('app_service_event');
    }


    /**
     * Get Service Post
     *
     * @return Post
     */
    private function getServicePost()
    {
        return $this->container->get('app_service_post');
    }

    /**
     * Get Service Page
     *
     * @return Page
     */
    private function getServicePage()
    {
        return $this->container->get('app_service_page');
    }

    /**
     * Get Service PageRelation
     *
     * @return PageRelation
     */
    private function getServicePageRelation()
    {
        return $this->container->get('app_service_page_relation');
    }

    /**
     * Get Service Conversation User
     *
     * @return ConversationUser
     */
    private function getServiceConversationUser()
    {
        return $this->container->get('app_service_conversation_user');
    }

    /**
     * Get Service User
     *
     * @return User
     */
    private function getServiceUser()
    {
        return $this->container->get('app_service_user');
    }

    /**
     * Get Service Mail.
     *
     * @return Mail
     */
    private function getServiceMail()
    {
        return $this->container->get('mail.service');
    }

    /**
     * Get Service Service Conversation User
     *
     * @return Fcm
     */
    private function getServiceFcm()
    {
        return $this->container->get('fcm');
    }
}

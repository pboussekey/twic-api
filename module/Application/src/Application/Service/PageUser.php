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
     * @param  int       $is_pinned
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
            ->setIsPinned($is_pinned);
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
            if (($state === ModelPageUser::STATE_MEMBER || $state === ModelPageUser::STATE_PENDING) && ModelPage::TYPE_ORGANIZATION === $m_page->getType() && $m_user->getOrganizationId() instanceof IsNull) {
                $this->getServiceUser()->_update($uid, null, null, null, null, null, null, null, null, null, $page_id);
            }
            // inviter only event
            if ($state === ModelPageUser::STATE_INVITED) {
                $this->getServicePost()->addSys(
                    'PPM'.$page_id.'_'.$uid,
                    '',
                    [
                    'state' => 'invited',
                    'user' => $uid,
                    'page' => $page_id,
                    'type' => $m_page->getType(),
                    ],
                    'invited',
                    ['M'.$uid]/*sub*/,
                    null/*parent*/,
                    $page_id/*page*/,
                    null/*user*/,
                    'page'
                );
                //$gcm_notification = new GcmNotification();
                /*$gcm_notification->setTitle($name)
                    ->setSound("default")
                    ->setColor("#00A38B")
                    ->setBody('Sent you a connection request');*/
                /*    $this->getServiceFcm()->send(
                    $uid, [
                    'data' => [
                        'type' => 'userpage',
                        'data' => [
                            'state' => 'invited',
                            'page' => $page_id,
                        ],
                    ],
                  ] //, $gcm_notification
                );

                */
                // member only group
            } elseif ($state === ModelPageUser::STATE_MEMBER) {
              

                $this->getServiceSubscription()->add('PP'.$page_id, $uid);
                if(ModelPage::TYPE_ORGANIZATION == $m_page->getType()) {
                    $res_page_relation = $this->getServicePageRelation()->getList($page_id, ModelPageRelation::TYPE_MEMBER);
                    foreach ($res_page_relation as $m_page_relation) {
                        $this->getServiceSubscription()->add("PP".$m_page_relation->getParentId(), $uid);
                    }
                }
                
                // Si il n'est pas le propriétaire on lui envoie une notification
                if ($m_page->getUserId() !== $uid) {
                    $this->getServicePost()->addSys(
                        'PPM'.$page_id.'_'.$uid,
                        '',
                        [
                        'state' => 'member',
                        'user' => $uid,
                        'page' => $page_id,
                        'type' => $m_page->getType(),
                        ],
                        'member',
                        ['M'.$uid]/*sub*/,
                        null/*parent*/,
                        $page_id/*page*/,
                        $uid/*user*/,
                        'page'
                    );
                    
                    /*
                                        $this->getServiceFcm()->send(
                                            $uid, [
                                            'data' => [
                                                'type' => 'userpage',
                                                'data' => [
                                                    'state' => 'member',
                                                    'page' => $page_id,
                                                ],
                                            ],
                                          ]
                                        );
                                        */
                }
            } else {
                /*    $this->getServiceFcm()->send(
                        $uid, [
                        'data' => [
                            'type' => 'userpage',
                            'data' => [
                                'state' => 'pending',
                                'page' => $page_id,
                            ],
                        ],
                      ]
                    );
                    */
            }
        }
        
        if ($m_page->getIsPublished() && $m_page->getType() == ModelPage::TYPE_COURSE) {   
            $identity = $this->getServiceUser()->getIdentity();
            $ar_pages = [];
            $ar_user = $this->getServiceUser()->getLite($user_id);
            foreach($ar_user as $m_user){
                if($m_user->getId() == $identity['id'] || $m_user->getHasEmailNotifier() === 0) {
                    continue;
                }
                $m_organization = false;
                if(!$m_user->getOrganizationId() instanceof IsNull) {
                    if(!array_key_exists($m_user->getOrganizationId(), $ar_pages)) {
                        $ar_pages[$m_user->getOrganizationId()] = $this->getServicePage()->getLite($m_user->getOrganizationId());
                    }
                    $m_organization = $ar_pages[$m_user->getOrganizationId()];
                }

                try{
                    
                    $prefix = ($m_organization !== false && is_string($m_organization->getLibelle()) && !empty($m_organization->getLibelle())) ?
                    $m_organization->getLibelle() : null;
                    $url = sprintf("https://%s%s/page/course/%s/timeline", ($prefix ? $prefix.'.':''), $this->container->get('config')['app-conf']['uiurl'], $m_page->getId());
                   $this->getServiceMail()->sendTpl(
                        'tpl_coursepublished', $m_user->getEmail(), [
                        'pagename' => $m_page->getTitle(),
                        'firstname' => $m_user->getFirstName(),
                        'pageurl' => $url,
                        ]
                    );
                    
                    $gcm_notification = new GcmNotification();
                    $gcm_notification->setTitle($m_page->getTitle())
                        ->setSound("default")
                        ->setColor("#00A38B")
                        ->setIcon("icon")
                        ->setTag("PAGECOMMENT".$t_page_id)
                        ->setBody("You have just been added to the course " . $m_page->getTitle());
                    
                    $this->getServiceFcm()->send($m_user->getId(), null, $gcm_notification, Fcm::PACKAGE_TWIC_APP);
                }
                catch (Exception $e) {
                    syslog(1, 'Model name does not exist <MESSAGE> ' . $e->getMessage() . '  <CODE> ' . $e->getCode());
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
                $this->getServicePost()->addSys(
                    'PPM'.$page_id.'_'.$user_id,
                    '',
                    [
                    'state' => 'member',
                    'user' => $user_id,
                    'page' => $page_id,
                    'type' => $m_page->getType(),
                    ],
                    'member',
                    ['M'.$user_id], /*sub ['M'.$user_id, 'PU'.$user_id] */
                    null/*parent*/,
                    null/*page*/,
                    $user_id/*user*/,
                    'page'
                );
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
        $m_page_user = $this->getModel()
            ->setPageId($page_id)
            ->setUserId($user_id);

        //si on suprime le dernier administrateur
        $ar_pu = $this->getListByPage($page_id, 'admin');
        if (count($ar_pu[$page_id]) === 1 && in_array($user_id, $ar_pu[$page_id])) {
            throw new Exception("On ne peut pas suprimer le dernier administrateur");
        }

        $ret =  $this->getMapper()->delete($m_page_user);
        if ($ret) {
            $this->getServiceSubscription()->delete('PP'.$page_id, $user_id);
            $res_page_relation = $this->getServicePageRelation()->getList($page_id, ModelPageRelation::TYPE_MEMBER);
            foreach ($res_page_relation as $m_page_relation) {
                $this->getServiceSubscription()->delete("PP".$m_page_relation->getParentId(), $user_id);
            }
            
            $this->getServicePost()->hardDelete('PPM'.$page_id.'_'.$user_id);
            // ON DELETE LES USER DANS LA CONVERSATION SI ELLE EXISTE
            $m_page = $this->getServicePage()->getLite($page_id);
            if (is_numeric($m_page->getConversationId())) {
                $this->getServiceConversationUser()->delete($m_page->getConversationId(), $user_id);
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
     * @param int       $is_pinned
     * @param string    $search
     * @param array    $order
     */
    public function getListByPage($page_id, $role = null, $state = null, 
        $sent = null, $is_pinned = null, $search = null, $order = null)
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
        $res_page_user = $this->getMapper()->getList($page_id, null, $role, $state, null, $is_admin ? null : $identity['id'], $sent, $is_pinned, $search, $order);
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
     * Get Service Subscription
     *
     * @return Subscription
     */
    private function getServiceSubscription()
    {
        return $this->container->get('app_service_subscription');
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

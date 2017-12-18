<?php
/**
 * TheStudnet (http://thestudnet.com)
 *
 * Page
 */
namespace Application\Service;

use Dal\Service\AbstractService;
use Application\Model\PageUser as ModelPageUser;
use Application\Model\PageRelation as ModelPageRelation;
use Application\Model\Page as ModelPage;
use Application\Model\Role as ModelRole;
use Application\Model\Conversation as ModelConversation;
use JRpc\Json\Server\Exception\JrpcException;
use ZendService\Google\Gcm\Notification as GcmNotification;
use Zend\Db\Sql\Predicate\IsNull;

/**
 * Class Page
 */
class Page extends AbstractService
{

    
    public function isAdmin($id)
    {
        $identity = $this->getServiceUser()->getIdentity();
        $ar_pu = $this->getServicePageUser()->getListByPage($id, ModelPageUser::ROLE_ADMIN);
        return (in_array($identity['id'], $ar_pu[$id]));
    }
    
    /**
     * Get custom Field
     *
     * @invokable
     *
     * @param string $libelle
     * @param int    $id
     */
    public function getCustom($libelle = null, $id = null)
    {
        $res_page = $this->getMapper()->getCustom($libelle, $id);

        if ($res_page->count() <= 0) {
            throw new JrpcException('No custom fields for ' . $libelle);
        }

        return $res_page->current();
    }

    /**
     * Add Page
     *
     * @invokable
     *
     * @param string $title
     * @param string $description
     * @param string $confidentiality
     * @param string $type
     * @param string $logo
     * @param string $admission
     * @param string $background
     * @param string $start_date
     * @param string $end_date
     * @param string $location
     * @param int    $page_id
     * @param array  $users
     * @param array  $tags
     * @param array  $docs
     * @param int    $owner_id
     * @param array  $address,
     * @param string $short_title,
     * @param string $website,
     * @param string $phone,
     * @param string $libelle,
     * @param string $custom,
     * @param string $subtype,
     * @param int    $circle_id
     * @param bool   $is_published
     *
     * @return int
     */
    public function add(
        $title,
        $description,
        $type,
        $confidentiality = null,
        $logo = null,
        $admission = null,
        $background = null,
        $start_date = null,
        $end_date = null,
        $location = null,
        $page_id = null,
        $users = [],
        $tags = [],
        $docs = [],
        $owner_id = null,
        $address = null,
        $short_title = null,
        $website = null,
        $phone = null,
        $libelle = null,
        $custom = null,
        $subtype = null,
        $circle_id = null,
        $is_published = null
    ) {
        
        if(null === $admission) {
            $admission = ModelPage::ADMISSION_INVITATION;
        }
        
        $identity = $this->getServiceUser()->getIdentity();

        //Si un non admin esaye de créer une organization
        if(!$this->getServiceUser()->isStudnetAdmin() && $type === ModelPage::TYPE_ORGANIZATION) {
            
            throw new JrpcException('Unauthorized operation page.add', -38003);
        }
        
        if(null === $page_id  && $type === ModelPage::TYPE_COURSE && (!$this->getServiceUser()->isStudnetAdmin()  || !$this->isAdmin($page_id))) {
            
            throw new JrpcException('Unauthorized operation page.add', -38003);
        }
        
        $user_id = $identity['id'];
        $formattedWebsite = $this->getFormattedWebsite($website);

        if (null === $confidentiality) {
            $confidentiality = ModelPage::CONFIDENTIALITY_PRIVATE;
        }
        if(!is_array($address)){
            $address = null;
        }

        $conversation_id = null;
        if ($type !== ModelPage::TYPE_ORGANIZATION) {
            $name = lcfirst(implode('', array_map("ucfirst", preg_split("/[\s]+/", preg_replace('/[^a-z0-9\ ]/', '', strtolower(str_replace('-', ' ', $title)))))));
            $conversation_id = $this->getServiceConversation()->_create(ModelConversation::TYPE_CHANNEL, null, null, $name);
        } else {
            $confidentiality = 0;
        }

        if (null === $owner_id) {
            $owner_id = $user_id;
        }
        
        if(null !== $start_date) {
            $start_date = (new \DateTime($start_date))->format('Y-m-d H:i:s');
        }       
        
        if(null !== $end_date) {
            $end_date = (new \DateTime($end_date))->format('Y-m-d H:i:s');
        }

        $m_page = $this->getModel()
            ->setTitle($title)
            ->setLogo($logo)
            ->setBackground($background)
            ->setDescription($description)
            ->setConfidentiality($confidentiality)
            ->setAdmission($admission)
            ->setStartDate($start_date)
            ->setEndDate($end_date)
            ->setLocation($location)
            ->setType($type)
            ->setUserId($user_id)
            ->setOwnerId($user_id)
            ->setCustom($custom)
            ->setLibelle($libelle)
            ->setWebsite($formattedWebsite)
            ->setPhone($phone)
            ->setIsPublished($is_published)
            ->setSubtype($subtype)
            ->setConversationId($conversation_id)
            ->setShortTitle($short_title)
            ->setCreatedDate((new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s'));

        if ($address !== null) {
            $address = $this->getServiceAddress()->getAddress($address);
            if ($address && null !== ($address_id = $address->getId())) {
                $m_page->setAddressId($address_id);
            }
        }

        $this->getMapper()->insert($m_page);
        $id = (int)$this->getMapper()->getLastInsertValue();

        if (null !== $page_id) {
            $mm_page = $this->getLite($page_id);
            if ($type === ModelPage::TYPE_ORGANIZATION && $mm_page->getType() === ModelPage::TYPE_ORGANIZATION) {
                $this->getServicePageRelation()->add($id, $page_id, ModelPageRelation::TYPE_MEMBER);
            } else {
                $this->getServicePageRelation()->add($id, $page_id, ModelPageRelation::TYPE_OWNER);
            }
        }

        if (null !== $circle_id) {
            $this->getServiceCircle()->addOrganizations($circle_id, $id);
        }

        if (!is_array($users)) {
            $users = [];
        }
        if (!is_array($docs)) {
            $docs = [];
        }

        $is_present = false;
        foreach ($users as $ar_u) {
            if(isset($ar_u['user_email'])) {
                $ar_u['user_id'] = $this->getServiceUser()->add(null, null, $ar_u['user_email'], null, null, null, null, null, null, null, $id);
            }
            if ($ar_u['user_id'] === $m_page->getOwnerId()) {
                $is_present = true;
                $ar_u['role'] = ModelPageUser::ROLE_ADMIN;
                $ar_u['state'] = ModelPageUser::STATE_MEMBER;

                break;
            }
        }

        if (! $is_present) {
            $users[] = [
              'user_id' => $m_page->getOwnerId(),
              'role' => ModelPageUser::ROLE_ADMIN,
              'state' => ModelPageUser::STATE_MEMBER
            ];
        }
        if (null !== $users) {
            $this->getServicePageUser()->_add($id, $users);
        }
        if (null !== $tags) {
            $this->getServicePageTag()->_add($id, $tags);
        }
        if (null !== $docs) {
            $this->getServicePageDoc()->_add($id, $docs);
        }

        if ($confidentiality === ModelPage::CONFIDENTIALITY_PUBLIC  && $type !== ModelPage::TYPE_ORGANIZATION) {
            $sub=[];
            if (null !== $page_id) {
                $sub[] = 'EP'.$page_id;
            } else {
                $sub[] = 'EU'.$owner_id;
            }

            $this->getServicePost()->addSys(
                'PP'.$id,
                '',
                [
                'state' => 'create',
                'user' => $owner_id,
                'parent' => $page_id,
                'page' => $id,
                'type' => $type,
                ],
                'create',
                null/*sub*/,
                null/*parent*/,
                $page_id/*page*/,
                $owner_id/*user*/,
                'page'
            );
        }

        return $id;
    }

    public function addChannel()
    {
        $res_page = $this->getMapper()->getListNoChannel();
        foreach ($res_page as $m_page) {
            $name = lcfirst(implode('', array_map("ucfirst", preg_split("/[\s]+/", preg_replace('/[^a-z0-9\ ]/', '', strtolower(str_replace('-', ' ', $m_page->getTitle())))))));
            $conversation_id = $this->getServiceConversation()->_create(ModelConversation::TYPE_CHANNEL, null, null, $name);
            $this->getMapper()->update(
                $this->getModel()
                    ->setId($m_page->getId())
                    ->setConversationId($conversation_id)
            );

            $ar_user = $this->getServicePageUser()->getListByPage($m_page->getId())[$m_page->getId()];
            $this->getServiceConversationUser()->add($conversation_id, $ar_user);
        }
    }
    /**
     * Add Tags
     *
     * @invokable
     *
     * @param int    $id
     * @param string $tag
     *
     * @return int
     */
    public function addTag($id, $tag)
    {
        return $this->getServicePageTag()->add($id, $tag);
    }

    /**
     * Remove Tags
     *
     * @invokable
     *
     * @param int $id
     * @param int $tag_id
     *
     * @return int
     */
    public function removeTag($id, $tag_id)
    {
        return $this->getServicePageTag()->remove($id, $tag_id);
    }

    /**
     * Add Document
     *
     * @invokable
     *
     * @param $id
     * @param $library
     **/
    public function addDocument($id, $library)
    {
        return $this->getServicePageDoc()->add($id, $library);
    }

    /**
     * Delete Document
     *
     * @invokable
     *
     * @param $library_id
     **/
    public function deleteDocument($library_id)
    {
        return $this->getServicePageDoc()->delete($library_id);
    }

    /**
     * Delete Document
     *
     * @invokable
     *
     * @param $library_id
     **/
    public function getListDocument($id, $filter = null)
    {
        return $this->getServiceLibrary()->getList($filter, null, null, null, null, $id);
    }

    /**
     * Update Page
     *
     * @invokable
     *
     * @param int    $id
     * @param string $title
     * @param string $logo
     * @param string $background
     * @param string $description
     * @param int    $confidentiality
     * @param string $admission
     * @param string $start_date
     * @param string $end_date
     * @param string $location
     * @param array  $users
     * @param array  $tags
     * @param array  $docs
     * @param int    $owner_id
     * @param int    $page_id
     * @param array  $address
     * @param string $short_title
     * @param string $website
     * @param string $phone
     * @param string $libelle
     * @param string $custom
     * @param int    $circle_id
     * @param bool   $is_published
     *
     * @TODO Seuls admins de la page peuvent l'éditer (ou un studnet admin)
     *
     * @return int
     */
    public function update(
        $id,
        $title=null,
        $logo=null,
        $background=null,
        $description=null,
        $confidentiality=null,
        $admission=null,
        $start_date = null,
        $end_date = null,
        $location = null,
        $users = null,
        $tags = null,
        $docs = null,
        $owner_id = null,
        $page_id = null,
        $address = null,
        $short_title = null,
        $website = null,
        $phone = null,
        $libelle = null,
        $custom = null,
        $circle_id = null,
        $is_published = null
    ) {
        if(!$this->getServiceUser()->isStudnetAdmin() &&  !$this->isAdmin($id)) {
            throw new JrpcException('Unauthorized operation page.update', -38003);
        }
        
        if(null !== $start_date) {
            $start_date = (new \DateTime($start_date))->format('Y-m-d H:i:s');
        }
        if(null !== $end_date) {
            $end_date = (new \DateTime($end_date))->format('Y-m-d H:i:s');
        }
        $user_id = $this->getServiceUser()->getIdentity()['id'];
        $formattedWebsite = $this->getFormattedWebsite($website);
        $m_page = $this->getModel()
            ->setId($id)
            ->setTitle($title)
            ->setLogo($logo)
            ->setBackground($background)
            ->setDescription($description)
            ->setConfidentiality($confidentiality)
            ->setAdmission($admission)
            ->setStartDate($start_date)
            ->setEndDate($end_date)
            ->setLocation($location)
            ->setUserId($user_id)
            ->setOwnerId($owner_id)
            ->setIsPublished($is_published)
            ->setCustom($custom)
            ->setLibelle($libelle)
            ->setWebsite($formattedWebsite)
            ->setPhone($phone)
            ->setShortTitle($short_title);

        if ($address !== null) {
            if($address === 0) {
                $m_page->setAddressId(new IsNull());
            }
            else{
                $address = $this->getServiceAddress()->getAddress($address);
                if ($address && null !== ($address_id = $address->getId())) {
                    $m_page->setAddressId($address_id);
                }
            }
        }

        if (null !== $users) {
            $is_present = false;
            foreach ($users as $ar_u) {
                if(isset($ar_u['user_email'])) {
                    $ar_u['user_id'] = $this->getServiceUser()->add(null, null, $ar_u['user_email'], null, null, null, null, null, null, null, $id);
                }
                if ($ar_u['user_id'] === $m_page->getOwnerId()) {
                    $is_present = true;
                    $ar_u['role'] = ModelPageUser::ROLE_ADMIN;
                    $ar_u['state'] = ModelPageUser::STATE_MEMBER;

                    break;
                }
            }
            $this->getServicePageUser()->replace($id, $users);
        }
        if (null !== $page_id) {
            $this->getServicePageRelation()->add($id, $page_id, ModelPageRelation::TYPE_OWNER);
        }
        if (null !== $circle_id) {
            $this->getServiceCircle()->addOrganizations($circle_id, $id);
        }
        if (null !== $tags) {
            $this->getServicePageTag()->replace($id, $tags);
        }
        if (null !== $docs) {
            $this->getServicePageDoc()->replace($id, $docs);
        }

        $tmp_m_page = $this->getMapper()->select($this->getModel()->setId($id))->current();
        if ($confidentiality !== null) {
            if ($tmp_m_page->getConfidentiality() !== $confidentiality) {
                if ($confidentiality == ModelPage::CONFIDENTIALITY_PRIVATE) {
                    $this->getServicePost()->hardDelete('PP'.$id);
                } elseif ($confidentiality == ModelPage::CONFIDENTIALITY_PUBLIC) {
                    $this->getServicePost()->addSys(
                        'PP'.$id,
                        '',
                        [
                        'state' => 'create',
                        'user' => $tmp_m_page->getOwnerId(),
                        'parent' => $tmp_m_page->getPageId(),
                        'page' => $id,
                        'type' => $tmp_m_page->getType(),
                        ],
                        'create',
                        null/*sub*/,
                        null/*parent*/,
                        $tmp_m_page->getPageId()/*page*/,
                        $tmp_m_page->getOwnerId()/*user*/,
                        'page'
                    );
                }
            }
        }

        if ($is_published === true) {
            $res_post = $this->getServicePost()->getListId(null, null, $id, null, true);
            foreach ($res_post as $m_post) {
                $this->getServicePostSubscription()->add(
                    'PP'.$id,
                    $m_post->getId(),
                    null,
                    'UPDATE',
                    $owner_id
                );
            }
            if(!$tmp_m_page->getIsPublished() && $tmp_m_page->getType() == ModelPage::TYPE_COURSE) {
                $ar_pages = [];
                $res_user = $this->getServiceUser()->getLite($this->getServicePageUser()->getListByPage($id)[$id]);
                foreach($res_user as $m_user){
                    if($m_user->getId() == $user_id) {
                        continue;
                    }
                    $m_organization = false;
                    if($m_user->getOrganizationId()) {
                        if(!array_key_exists($m_user->getOrganizationId(), $ar_pages)) {
                            $ar_pages[$m_user->getOrganizationId()] = $this->getLite($m_user->getOrganizationId());
                        }
                        $m_organization = $ar_pages[$m_user->getOrganizationId()];
                    }
                    
                    try{
                        
                        $prefix = ($m_organization !== false && is_string($m_organization->getLibelle()) && !empty($m_organization->getLibelle())) ?
                        $m_organization->getLibelle() : null;
                        
                        $url = sprintf("https://%s%s/page/course/%s/timeline", ($prefix ? $prefix.'.':''),  $this->container->get('config')['app-conf']['uiurl'], $tmp_m_page->getId());
                        $this->getServiceMail()->sendTpl(
                            'tpl_coursepublished', $m_user->getEmail(), [
                            'pagename' => $tmp_m_page->getTitle(),
                            'firstname' => $m_user->getFirstName(),
                            'pageurl' => $url
                            ]
                        );
                        
                        $gcm_notification = new GcmNotification();
                        $gcm_notification->setTitle($tmp_m_page->getTitle())
                            ->setSound("default")
                            ->setColor("#00A38B")
                            ->setIcon("icon")
                            ->setTag("PAGECOMMENT".$t_page_id)
                            ->setBody("You have just been added to the course " . $tmp_m_page->getTitle());
                        
                        $this->getServiceFcm()->send($m_user->getId(), null, $gcm_notification);
                    }
                    catch (\Exception $e) {
                        syslog(1, 'Model name does not exist Page publish <MESSAGE> ' . $e->getMessage() . '  <CODE> ' . $e->getCode());
                    }
                }
               

            }
            
        }

        if (is_numeric($tmp_m_page->getConversationId()) && null !== $title) {
            $name = lcfirst(implode('', array_map("ucfirst", preg_split("/[\s]+/", preg_replace('/[^a-z0-9\ ]/', '', strtolower(str_replace('-', ' ', $title)))))));
            $conversation_id = $this->getServiceConversation()->update($tmp_m_page->getConversationId(), $name);
        }

        return $this->getMapper()->update($m_page);
    }



    /**
     * Delete Page
     *
     * @invokable
     *
     * @param  int $id
     * @return int
     */
    public function delete($id)
    {
        if (!is_array($id)) {
            $id = [$id];
        }
        $m_page = $this->getModel()->setId($id)
            ->setDeletedDate((new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s'));
        if ($this->getMapper()->update($m_page)) {
            foreach ($id as $i) {
                $this->getServicePost()->hardDelete('PP'.$i);
                $m_tmp_page = $this->getLite($i);
                if ($m_tmp_page->getType() === ModelPage::TYPE_ORGANIZATION) {
                    $this->getServiceUser()->removeOrganizationId($i);
                }
            }
            return true;
        }

        return false;
    }

    /**
     * Reactivate Page
     *
     * @invokable
     *
     * @param  int $id
     * @return int
     */
    public function reactivate($id)
    {
        $m_page = $this->getModel()->setId($id)->setDeletedDate(new \Zend\Db\Sql\Predicate\IsNull());

        return $this->getMapper()->update($m_page);
    }

    /**
     * Get Page
     *
     * @invokable
     *
     * @param int    $id
     * @param int    $parent_id
     * @param string $type
     */
    public function get($id = null, $parent_id = null, $type = null)
    {
        $this->addChannel();
        if (null === $id && null === $parent_id) {
            throw new \Exception('Error: params is null');
        }
        $identity = $this->getServiceUser()->getIdentity();
        $is_admin = (in_array(ModelRole::ROLE_ADMIN_STR, $identity['roles']));

        $res_page = $this->getMapper()->get($identity['id'], $id, $parent_id, $type, $is_admin);

        foreach ($res_page as $m_page) {
            $m_page->setTags($this->getServicePageTag()->getList($m_page->getId()));
            $this->getOwner($m_page);
        }

        $res_page->rewind();

        if (is_array($id)) {
            $ar_page = $res_page->toArray(['id']);
            foreach ($id as $i) {
                if (!isset($ar_page[$i])) {
                    $ar_page[$i] = null;
                }
            }
        }

        return (is_array($id)) ? $ar_page : $res_page->current();
    }

    /**
     * Get Page Lite
     *
     * @invokable
     *
     * @param  int $id
     * @return \Application\Model\Page
     */
    public function getLite($id = null, $conversation_id = null)
    {

        return $this->getMapper()->select($this->getModel()->setId($id)->setConversationId($conversation_id))->current();
    }
    
      /**
       * Get list suscribed users
       *
       * @invokable
       *
       * @param  int $id
       * @return array
       */
    public function getListSuscribersId($id, $filter)
    {
        return $this->getServiceSubscription()->getListUserId('PP'.$id, $filter);;
    }

    

    /**
     * Get Page
     *
     * @invokable
     *
     * @param  int $item_id
     * @return int
     */
    public function getIdByItem($item_id)
    {
        $this->addChannel();
        return $this->getMapper()->getIdByItem($item_id)->current()->getId();
    }

    /**
     * Get Page grades
     *
     * @invokable
     *
     * @param int $id
     */
    public function getGrades($id)
    {
        if (!is_array($id)) {
            $id = [$id];
        }

        $grades = [];
        foreach ($id as $i) {
            $median = $this->getMapper()->getMedian($i)->current()->getMedian();
            $avg = $this->getMapper()->getAverage($i)->current()->getAverage();
            $grades[$i] =  [
                'id' => $i,
                'median' => is_numeric($median) ? $median : null,
                'average' => is_numeric($avg) ? $avg : null
             ];
        }
        return $grades;
    }


    /**
     * Get Page grades
     *
     * @invokable
     *
     * @param int   $id
     * @param array $filter
     */
    public function getUsersGrades($id, $filter)
    {
        $res_grades = $this->getMapper()->usePaginator($filter)->getUsersAvg($id);
        $res_prcs = $this->getMapper()->getUsersPrc($id);
        $prcs = [];
        foreach ($res_prcs as $m_prc) {
            $prcs[$m_prc->getAverage()] = $m_prc->getPercentile();
        }
        foreach ($res_grades as $m_grade) {
            $m_grade->setPercentile($prcs[$m_grade->getAverage()]);
        }

        return [ 'list' => $res_grades, 'count' => $this->getMapper()->count() ];
    }
    
     /**
      * Get user grades per pages
      *
      * @invokable
      *
      * @param int $id
      * @param int $user_id
      */
    public function getUserGrades($id, $user_id)
    {
        if(!is_array($user_id)) {
            $user_id = [$user_id];
        }
        $var_grades = [];
        foreach($user_id as $u){
            $var_grades[$u] = $this->getMapper()->getUserGrades($id, $u);
        }
        return $var_grades;
    }

    /**
     * Get Page
     *
     * @invokable
     *
     * @param int    $parent_id
     * @param string $type
     * @param string $start_date
     * @param string $end_date
     * @param int    $member_id
     * @param array  $filter
     * @param bool   $strict_dates
     * @param string $search
     * @param array  $tags
     * @param int    $children_id
     *
     * @throws \Exception
     * @return \Dal\Db\ResultSet\ResultSet
     */
    public function getListId(
        $parent_id = null,
        $type = null,
        $start_date = null,
        $end_date = null,
        $member_id = null,
        $filter = null,
        $strict_dates = false,
        $search = null,
        $tags = null,
        $children_id = null,
        $is_member_admin = null, // get only les meber admin true/false
        $exclude = null
    ) {
        $this->addChannel();
        if (empty($tags)) {
            $tags = null;
        }
        $identity = $this->getServiceUser()->getIdentity();
        $is_admin = (in_array(ModelRole::ROLE_ADMIN_STR, $identity['roles']));

        $mapper = $this->getMapper()->usePaginator($filter);
        $res_page = $mapper->getListId($identity['id'], $parent_id, $type, $start_date, $end_date, $member_id, $strict_dates, $is_admin, $search, $tags, $children_id, $is_member_admin, null, $exclude);

        $ar_page = [];
        foreach ($res_page as $m_page) {
            $ar_page[] = $m_page->getId();
        }

        return [
          'list' => $ar_page,
          'count' => $mapper->count()
        ];
    }

    /**
     * Get Page
     *
     * @invokable
     *
     * @param int $parent_id
     * @param int $children_id
     *
     * @throws \Exception
     * @return \Dal\Db\ResultSet\ResultSet
     */
    public function getListRelationId($parent_id = null, $children_id = null)
    {
        if (empty($tags)) {
            $tags = null;
        }
        $identity = $this->getServiceUser()->getIdentity();
        $is_admin = (in_array(ModelRole::ROLE_ADMIN_STR, $identity['roles']));
        $res_page = $this->getMapper()->getListId($identity['id'], $parent_id, ModelPage::TYPE_ORGANIZATION, null, null, null, null, $is_admin, null, null, $children_id, null, ModelPageRelation::TYPE_MEMBER);

        $ar_page = [];

        if (null !== $parent_id) {
            if (!is_array($parent_id)) {
                $parent_id = [$parent_id];
            }
            foreach ($parent_id as $pi) {
                $ar_page[$pi] = [];
            }
        }

        if (null !== $children_id) {
            if (!is_array($children_id)) {
                $children_id = [$children_id];
            }
            foreach ($children_id as $ci) {
                $ar_page[$ci] = [];
            }
        }

        foreach ($res_page as $m_page) {
            if (is_numeric($m_page->getPageRelation()->getParentId())) {
                $ar_page[$m_page->getPageRelation()->getParentId()][] = $m_page->getId();
            }
            if (is_numeric($m_page->getPageRelation()->getPageId())) {
                $ar_page[$m_page->getPageRelation()->getPageId()][] = $m_page->getId();
            }
        }

        return $ar_page;
    }

    /**
     * Generate a formatted website url for the school.
     *
     * @param string $website
     *
     * @return string
     */
    private function getFormattedWebsite($website)
    {
        $hasProtocol = strpos($website, 'http://') === 0 || strpos($website, 'https://') === 0 || strlen($website) === 0;
        return $hasProtocol ? $website : 'http://' . $website;
    }

    /**
     * Get owner string by Page Model
     *
     * @param \Application\Model\Page $m_page
     */
    private function getOwner(\Application\Model\Page $m_page)
    {
        $owner = [];
        $res_page = $this->getServicePageRelation()->getOwner($m_page->getId());
        switch (true) {
        case $res_page->count() > 0:
            $ar_page = $this->getLite($res_page->current()->getParentId())->toArray();
            $owner = [
                'id' => $ar_page['id'],
                'text' => $ar_page['title'],
                'img' => $ar_page['logo'],
                'type' => $ar_page['type'],
            ];
            break;
        case is_numeric($m_page->getOwnerId()):
            $ar_user = $m_page->getUser()->toArray();
            $owner = [
                'id' => $ar_user['id'],
                'text' => $ar_user['firstname'] . ' ' . $ar_user['lastname'],
                'img' => $ar_user['avatar'],
                'type' => 'user',
            ];
            break;

        }

        $m_page->setOwner($owner);
    }

    
     /**
      * Get page counts.
      *
      * @invokable
      *
      * @param string       $start_date
      * @param string       $end_date
      * @param string       $interval_date
      * @param array|string $type
      * @param int          $organization_id
      *
      * @return array
      */
    public function getCount( $start_date = null, $end_date = null, $interval_date = 'D', $type = null, $organization_id  = null)
    {
        
        $interval = $this->getServiceActivity()->interval($interval_date);
        $identity = $this->getServiceUser()->getIdentity();
        
        return $this->getMapper()->getCount($identity['id'], $interval, $start_date, $end_date, $organization_id, $type);
    }




    public function getByConversationId($conversation_id)
    {
        return $this->getMapper()->select($this->getModel()->setConversationId($conversation_id))->current();
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
     * Get Service Page User
     *
     * @return \Application\Service\PageUser
     */
    private function getServicePageUser()
    {
        return $this->container->get('app_service_page_user');
    }

    /**
     * Get Service Page Doc
     *
     * @return \Application\Service\PageDoc
     */
    private function getServicePageDoc()
    {
        return $this->container->get('app_service_page_doc');
    }

    /**
     * Get Service Page Tag
     *
     * @return \Application\Service\PageTag
     */
    private function getServicePageTag()
    {
        return $this->container->get('app_service_page_tag');
    }

    /**
     * Get Service Address
     *
     * @return \Address\Service\Address
     */
    private function getServiceAddress()
    {
        return $this->container->get('addr_service_address');
    }

    /**
     * Get Service Cicle
     *
     * @return \Application\Service\Circle
     */
    private function getServiceCircle()
    {
        return $this->container->get('app_service_circle');
    }

    /**
     * Get Service Cicle
     *
     * @return \Application\Service\PageRelation
     */
    private function getServicePageRelation()
    {
        return $this->container->get('app_service_page_relation');
    }

    /**
     * Get Service Conversation
     *
     * @return \Application\Service\Conversation
     */
    private function getServiceConversation()
    {
        return $this->container->get('app_service_conversation');
    }

    /**
     * Get Service Conversation User
     *
     * @return \Application\Service\ConversationUser
     */
    private function getServiceConversationUser()
    {
        return $this->container->get('app_service_conversation_user');
    }

    /**
     * Get Service Library
     *
     * @return \Application\Service\Library
     */
    private function getServiceLibrary()
    {
        return $this->container->get('app_service_library');
    }

    /**
     * Get Service Post Subscription
     *
     * @return \Application\Service\PostSubscription
     */
    private function getServicePostSubscription()
    {
        return $this->container->get('app_service_post_subscription');
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
     * Get Service Post
     *
     * @return \Application\Service\Post
     */
    private function getServicePost()
    {
        return $this->container->get('app_service_post');
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
}

<?php
namespace Application\Service;

use Dal\Service\AbstractService;
use ZendService\Google\Gcm\Notification as GcmNotification;
use Application\Model\Item as ModelItem;
use Application\Model\Role as ModelRole;
use Dal\Db\ResultSet\ResultSet;
use Application\Model\Conversation as ModelConversation;
use Zend\Db\Sql\Predicate\IsNull;
use JRpc\Json\Server\Exception\JrpcException;
use Application\Service\Event as ModelEvent;

class Item extends AbstractService
{

    private static $id = 0;
    /**
     * Add item
     *
     * @invokable
     *
     * @param int    $page_id
     * @param string $title
     * @param int    $points
     * @param string $description
     * @param string $type
     * @param bool   $is_available
     * @param bool   $is_published
     * @param int    $order_id
     * @param string $start_date
     * @param string $end_date
     * @param int    $parent_id
     * @param int    $library_id
     * @param int    $post_id
     * @param string $text
     * @param array  $participants
     * @param int    $quiz_id
     * @param bool   $is_grade_published
     */
    public function add($page_id, $title, $points = null, $description = null, $type = null, $is_available = null, $is_published = null, $order_id = null, $start_date = null, $end_date = null, $parent_id = null, $library_id = null, $post_id = null, $text = null, $participants = null, $quiz_id = null, $is_grade_published = null)
    {
        $identity = $this->getServiceUser()->getIdentity();


        if(null !== $start_date) {
            $start_date = (new \DateTime($start_date))->format('Y-m-d H:i:s');
        }

        if(null !== $end_date) {
            $end_date = (new \DateTime($end_date))->format('Y-m-d H:i:s');
        }

        if (!$this->getServiceUser()->isStudnetAdmin() && !$this->getServicePage()->isAdmin($page_id)) {
            throw new JrpcException('Unauthorized operation item.add', -38003);
        }

        $user_id = $identity['id'];
        $m_item = $this->getModel()
            ->setPageId($page_id)
            ->setTitle($title)
            ->setPoints($points)
            ->setDescription($description)
            ->setType($type)
            ->setIsAvailable($is_available)
            ->setIsPublished($is_published)
            ->setStartDate($start_date)
            ->setEndDate($end_date)
            ->setLibraryId($library_id)
            ->setText($text)
            ->setIsGradePublished($is_grade_published)
            ->setParticipants($participants)
            ->setCreatedDate((new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s'))
            ->setUserId($user_id);

        if($type === ModelItem::TYPE_LIVE_CLASS) {
            $m_item->setConversationId($this->getServiceConversation()->_create(ModelConversation::TYPE_LIVECLASS, null, true));
        }

        $this->getMapper()->insert($m_item);

        $id = (int) $this->getMapper()->getLastInsertValue();

        if (null !== $post_id) {
            $this->getServicePost()->_update($post_id, null, null, null, null, null, null, null, null, null, null, null, null, null, $id);
        }
        if (null !== $quiz_id) {
            $this->getServiceQuiz()->update($quiz_id, $id);
        }

        $this->move($id, - 1, $parent_id);

        return $id;
    }

    /**
     * Move Item
     *
     * @invokable
     *
     * @param int $id
     * @param int $order_id
     * @param int $parent_id
     */
    public function move($id, $order_id = null, $parent_id = null)
    {

        if (null !== $parent_id) {
            $this->getMapper()->update(
                $this->getModel()
                    ->setParentId($parent_id)
                    ->setId($id)
            );
        }

        $m_base_order = $this->getMapper()
            ->select(
                $this->getModel()
                    ->setId($id)
            )
            ->current();

        if (!$this->getServiceUser()->isStudnetAdmin() && !$this->getServicePage()->isAdmin($m_base_order->getPageId())) {
            throw new JrpcException('Unauthorized operation item.move', -38003);
        }
        if (- 1 === $order_id || (null === $order_id && null !== $parent_id)) {
            $order = 1;
            // on récuper l'ordre le plus grand +1
            $res_order_last = $this->getMapper()->getLastOrder($id, $m_base_order->getPageId(), $m_base_order->getParentId());
            if ($res_order_last->count() > 0) {
                $order = $res_order_last->current()->getOrder() + 1;
            }

            // on atribut l'ordre
            $this->getMapper()->update(
                $this->getModel()
                    ->setId($id)
                    ->setOrder($order)
            );
        } elseif (is_numeric($order_id)) {
            // on verirfie si il existe une ordre superieur

            if ($order_id !== 0) {
                $m_order = $this->getMapper()
                    ->select(
                        $this->getModel()
                            ->setId($order_id)
                    )
                    ->current();
                $order = ($m_order->getOrder() + 1);
            } else {
                $order = 1;
            }

            $res_order_sup = $this->getMapper()->select(
                $this->getModel()
                    ->setOrder($order)
            );
            if ($res_order_sup->count() > 0) {
                // si oui on decaler
                $this->getMapper()->uptOrder($m_base_order->getPageId(), $order, $m_base_order->getParentId());
            }

            // on atribut l'ordre
            $this->getMapper()->update(
                $this->getModel()
                    ->setId($id)
                    ->setOrder($order)
            );
        }
    }

    /**
     * GetList User of Item
     *
     * @invokable
     *
     * @param int $id
     */
    public function getListItemUser($id)
    {
        if (! is_array($id)) {
            $id = [
                $id
            ];
        }
        $arr_item_user = [];
        foreach ($id as $i) {
            $arr_item_user[$i] = [];
        }
        $res_item_user = $this->getServiceItemUser()->getList($id);
        foreach ($res_item_user as $m_item_user) {
            $arr_item_user[$m_item_user->getItemId()][] = $m_item_user->toArray();
        }

        return $arr_item_user;
    }

    /**
     * Add User In Item
     *
     * @invokable
     *
     * @param int       $id
     * @param int|array $user_ids
     * @param int       $group_id
     * @param string    $group_name
     */
    public function addUsers($id, $user_ids, $group_id = null, $group_name = null)
    {
        $me = $this->getServiceUser()->getIdentity()['id'];
        $m_group = $this->getServiceGroup()->getOrCreate($group_name, $id, $group_id);
        if (null === $group_id && null !== $group_name) {
            $group_id = $m_group->getId();
        }
        else if(null !== $group_id && null !== $group_name){
            $this->getServiceGroup()->update($group_id, $group_name);
        }

        $res = $this->getServiceItemUser()->addUsers($id, $user_ids, $group_id);
        $m_item = $this->get($id);
        $res_item_user = $this->getServiceItemUser()->getList($id, null, null, $group_id);
        $sub = [];
        $users = [];
        foreach($res_item_user as $m_item_user){
            $sub[] = 'M'.$m_item_user->getUserId();
            $users[] = $m_item_user->getUserId();
        }
        $this->getServicePost()->addSys(
            'GR'.$group_id,
            '',
            [
              'users' => $users,
              'id' => $group_id,
              'name' => $group_name,
              'item' => $m_item->getId()
            ],
            'member',
            $sub/*sub*/,
            null/*parent*/,
            null/*page*/,
            null/*user*/,
            'group',
            $m_item->getPageId(),
            $m_item->getId(),
            true,
            false
        );
        return $res;
    }

    /**
     * Delete User In Item
     *
     * @invokable
     *
     * @param int       $id
     * @param int|array $user_ids
     */
    public function deleteUsers($id, $user_ids)
    {
        return $this->getServiceItemUser()->deleteUsers($id, $user_ids);
    }


    /**
    * GetList available items count for a page
    *
    * @invokable
    *
    * @param int  $page_id
    */
    public function getCountByPage($page_id)
    {
        $identity = $this->getServiceUser()->getIdentity();
        $res_item = $this->getMapper()->getListId($page_id, $identity['id'],  $this->getServicePage()->isAdmin($page_id), 0);

        return count($res_item->toArray());
    }

    /**
     * GetList Id Item
     *
     * @invokable
     *
     * @param int  $page_id
     * @param int  $parent_id
     * @param bool $is_publish // true - que les item publié
     */
    public function getListId($page_id = null, $parent_id = null, $is_publish = null)
    {
        $identity = $this->getServiceUser()->getIdentity();

        if (is_array($page_id)) {
            $page_id = reset($page_id);
        }
        if (null === $page_id) {
            $page_id = $this->getServicePage()->getIdByItem($parent_id);
        }

        $ar_pu = $this->getServicePageUser()->getListByPage($page_id, 'admin');
        $is_admin_page = (in_array($identity['id'], $ar_pu[$page_id])) || (in_array(ModelRole::ROLE_ADMIN_STR, $identity['roles']));

        $res_item = $this->getMapper()->getListId($page_id, $identity['id'], $is_admin_page, $parent_id, $is_publish);

        $index = ($parent_id === null) ? $page_id : $parent_id;

        if (is_array($index)) {
            foreach ($index as $i) {
                $ar_item[$i] = [];
            }
        } else {
            $ar_item[$index] = [];
        }

        foreach ($res_item as $m_item) {
            $ii = (! is_numeric($m_item->getParentId())) ? $m_item->getPageId() : $m_item->getParentId();
            $ar_item[$ii][] = $m_item->getId();
        }

        return $ar_item;
    }

    /**
     * GetList Assignment Id Item
     *
     * @invokable
     *
     * @param int   $page_id
     * @param array $filter
     */
    public function getListAssignmentId($page_id = null, $filter = null)
    {
        $identity = $this->getServiceUser()->getIdentity();
        $ar_item = [];

        if (null !== $page_id) {
            if (! is_array($page_id)) {
                $page_id = [$page_id];
            }

            foreach ($page_id as $p_id) {
                $ar_item[$p_id] = [];
                $ar_pu = $this->getServicePageUser()->getListByPage($p_id, 'admin');
                $is_admin_page = (in_array($identity['id'], $ar_pu[$p_id]));

                $res_item = $this->getMapper()->getListAssignmentId($identity['id'], $p_id, $filter, $is_admin_page);
                foreach ($res_item as $m_item) {
                    $ar_item[$m_item->getPageId()][] = $m_item->getId();
                }
            }
        } else {
            $res_item = $this->getMapper()->getListAssignmentId($identity['id'], null, $filter);
            foreach ($res_item as $m_item) {
                $ar_item[] = $m_item->getId();
            }
        }

        return $ar_item;
    }

    /**
     * GetList TimeLine Item
     *
     * @invokable
     *
     * @param array $filter
     */
    public function getListTimeline($filter = [])
    {
        $identity = $this->getServiceUser()->getIdentity();
        $mapper = $this->getMapper();
        $ret = $mapper->usePaginator($filter)
            ->getListTimeline($identity['id']);

        return ['list' => $ret, 'count' => $mapper->count()];
    }

    /**
     * Get Info Item
     *
     * @invokable
     *
     * @param int|array $id
     */
    public function getInfo($id)
    {
        if (! is_array($id)) {
            $id = [
                $id
            ];
        }

        // TODO check admin page
        $ar = [];
        foreach ($id as $i) {
            $ar[$i] = $this->getMapper()
                ->getInfo($i)
                ->current()
                ->toArray();
        }

        return $ar;
    }

    /**
     * Get Info Item
     *
     * @invokable
     *
     * @param int|array $id
     */
    public function getListSubmission($id)
    {
        $identity = $this->getServiceUser()->getIdentity();
        if (! is_array($id)) {
            $id = [
                $id
            ];
        }

        $ar = [];
        foreach ($id as $i) {
            $paticipants = $this->getMapper()
                ->select(
                    $this->getModel()
                        ->setId($i)
                )
                ->current()
                ->getParticipants();
            $page_id = $this->getLite($i)
                ->current()
                ->getPageId();
            $ar_pu = $this->getServicePageUser()->getListByPage($page_id, 'admin');
            $is_admin = (in_array($identity['id'], $ar_pu[$page_id])  || (in_array(ModelRole::ROLE_ADMIN_STR, $identity['roles'])));
            $res_item = $this->getMapper()->getListSubmission($i, ! $is_admin ? $identity['id'] : null);
            switch ($paticipants) {
            case 'all':
                foreach ($res_item as $m_item) {
                    $ar_item = $m_item->toArray();
                    $tmpar = [
                        'group_id' => null,
                        'rate' => ($m_item->getIsGradePublished() == true || $is_admin) ? $ar_item['item_user']['rate'] : null,
                        'users' => [
                            $ar_item['page_user']['user_id']
                        ],
                        'submit_date' => $ar_item['item_user']['submission']['submit_date'],
                        'post_id' => $ar_item['item_user']['submission']['post_id'],
                        'item_id' => $i
                    ];
                    if (! $is_admin) {
                        $ar[$i] = $tmpar;
                    } else {
                        $ar[$i][] = $tmpar;
                    }
                }
                break;
            case 'user':
                foreach ($res_item as $m_item) {
                    if (is_numeric($m_item->getItemUser()->getId())) {
                        $ar_item = $m_item->toArray();
                        $tmpar = [
                            'group_id' => null,
                            'rate' => ($m_item->getIsGradePublished() == true || $is_admin) ? $ar_item['item_user']['rate'] : null,
                            'users' => [
                                $ar_item['page_user']['user_id']
                            ],
                            'submit_date' => $ar_item['item_user']['submission']['submit_date'],
                            'post_id' => $ar_item['item_user']['submission']['post_id'],
                            'item_id' => $i
                        ];
                        if (! $is_admin) {
                            $ar[$i] = $tmpar;
                        } else {
                            $ar[$i][] = $tmpar;
                        }
                    }
                }
                break;
            case 'group':
                $groups = [];
                foreach ($res_item as $m_item) {
                    if (is_numeric($m_item->getItemUser()->getId())) {
                        $ar_item = $m_item->toArray();
                        $groupId = $ar_item['item_user']['group_id'];
                        if (isset($groups[$groupId])) {
                            $groups[$groupId]['users'][] = $ar_item['page_user']['user_id'];
                        } else {
                            $groups[$groupId] = [
                                'group_id' => $ar_item['item_user']['group_id'],
                                'group_name' => $ar_item['item_user']['group']['name'],
                                'rate' => ($m_item->getIsGradePublished() == true || $is_admin) ? $ar_item['item_user']['rate'] : null,
                                'users' => [$ar_item['page_user']['user_id']],
                                'submit_date' => $ar_item['item_user']['submission']['submit_date'],
                                'post_id' => $ar_item['item_user']['submission']['post_id'],
                                'item_id' => $i
                            ];
                            if (! $is_admin) {
                                $groups[$groupId]['users'] = $this->getServiceItemUser()->getListUserId($ar_item['item_user']['group_id']);
                                $ar[$i] = &$groups[$groupId];
                            } else {
                                $ar[$i][] = &$groups[$groupId];
                            }
                        }
                    }
                }

                break;
            default:
                break;
            }
        }

        return $ar;
    }

    /**
     * Get Item
     *
     * @invokable
     *
     * @param  int|array $id
     * @return \Application\Model\Item
     */
    public function get($id)
    {
	$identity = $this->getServiceUser()->getIdentity();
        $is_admin = (in_array(ModelRole::ROLE_ADMIN_STR, $identity['roles']));
        $res_item = $this->getMapper()->get($id, $identity['id'], $is_admin);

        return (is_array($id)) ? $res_item->toArray(
            [
            'id'
            ]
        ) : $res_item->current();
    }

    /**
     * Grade Item
     *
     * @invokable
     *
     * @param int       $item_id
     * @param int       $rate
     * @param int|array $user_id
     * @param int|array $group_id
     */
    public function grade($item_id, $rate, $user_id = null, $group_id = null)
    {
        return $this->getServiceItemUser()->grade($item_id, $rate, $user_id, $group_id);
    }

    /**
     * Publish Item
     *
     * @param  int    $id
     * @param  string $publish
     * @param  string $all
     * @param  int    $parent_id
     * @param  bool   $notify
     * @throws \Exception
     *
     * @return boolean
     */
    public function publish($id = null, $publish = true, $all = false, $parent_id = null, $notify = false)
    {
        if (null === $id && null === $parent_id) {
            throw new \Exception("Error Processing Request", 1);// @codeCoverageIgnore
        }

        $page_id = $this->getLite((null !== $id) ? $id : $parent_id)
            ->current()
            ->getPageId();
        $identity = $this->getServiceUser()->getIdentity();

        $this->getMapper()->update(
            $this->getModel()
                ->setId($id)
                ->setParentId($parent_id)
                ->setIsPublished($publish)
        );

        /*if (true === $all) {
            if (null !== $id) {
                $this->publish(null, $publish, true, $id);
            } else {
                $res_item = $this->getMapper()->select($this->getModel()
                    ->setParentId($parent_id));
                foreach ($res_item as $m_item) {
                    $this->publish(null, $publish, true, $m_item->getId());
                }
            }
        }*/

        $m_item = $this->getLite($id)->current();
        if(($publish == true || $publish === null && $m_item->getIsPublished() == true) && $notify == true) {
            $m_page = $this->getServicePage()->getLite($page_id);
            if($m_page->getIsPublished() == true) {
                // SI il y a un parent
                if(is_numeric($m_item->getParentId())) {
                    // SI SONT PARENT IS PAS PUBLIER ON SORT
                    $m_item_parent = $this->getLite($m_item->getParentId())->current();
                    if(!$m_item_parent->getIsPublished()) {
                        return true;
                    }
                    //IF THERE IS MORE THAN TWO LEVELS OF ITEMS
                    /*if(is_numeric($m_item_parent->getParentId())) {
                        $m_item_section = $this->getLite($m_item_parent->getParentId())->current();
                        if(!$m_item_section->getIsPublished()) {
                            return true;
                        }
                    }*/
                }
                if($m_item->getType() === ModelItem::TYPE_SECTION){
                    $res_children= $this->getListId(null, $m_item->getId(), true)[$m_item->getId()];
                    foreach($res_children as $child){
                        $this->publish($child, null, null, $m_item->getId(), false);
                    }
                    if(count($res_children) > 0){
                        $this->getServiceEvent()->create(
                            'section', 'publish', 'ITMUPD'.$m_item->getId(),
                            ["PP".$page_id],
                            [
                                'page'    => $page_id,
                                'page_type' => $m_page->getType(),
                                'picture' => !($m_page->getLogo() instanceof IsNull) ? $m_page->getLogo() : null
                            ],
                            [
                              'pagetitle' => $m_page->getTitle(),
                              'pagelogo' => $m_page->getLogo(),
                              'itemtitle' => $m_item->getTitle(),
                              'children' => count($res_children),
                              'itemtype' => ModelItem::type_relation[$m_item->getType()]
                            ],
                            ['fcm' => Fcm::PACKAGE_TWIC_APP, 'mail' => true]
                        );
                    }

                    return true;
                }


                $this->getServiceEvent()->create(
                    'item', 'publish','ITMUPD'.$m_item->getId(),
                    ["PP".$page_id],
                    [
                        'item' => $id,
                        'page'    => $page_id,
                        'page_type' => $m_page->getType(),
                        'picture' => !($m_page->getLogo() instanceof IsNull) ? $m_page->getLogo() : null
                    ],
                    [
                      'pagetitle' => $m_page->getTitle(),
                      'pagelogo' => $m_page->getLogo(),
                      'itemtitle' => $m_item->getTitle(),
                      'itemtype' => ModelItem::type_relation[$m_item->getType()]
                    ],
                    ['fcm' => Fcm::PACKAGE_TWIC_APP, 'mail' => true]
                );
                $res_group = $this->getServiceGroup()->getList($m_item->getId())[$m_item->getId()];
                foreach($res_group as $group){
                    $res_item_user = $this->getServiceItemUser()->getList($group['item_id'], null, null, $group['id']);
                    $users = [];
                    $sub = [];
                    foreach($res_item_user as $m_item_user){
                        $sub[] = 'M'.$m_item_user->getUserId();
                        $users[] = $m_item_user->getUserId();
                    }
                    $this->getServicePost()->addSys(
                        'GR'.$group['id'],
                        '',
                        [
                          'users' => $users,
                          'id' => $group['id'],
                          'name' => $group['name'],
                          'item' => $group['item_id']
                        ],
                        'member',
                        $sub/*sub*/,
                        null/*parent*/,
                        null/*page*/,
                        null/*user*/,
                        'group',
                        $page_id,
                        $group['item_id']
                    );
                }
            }
        }

        return true;
    }

    /**
     * Get Lite Item
     *
     * @param int $id
     * @param int $conversation_id
     *
     * @return ResultSet|\Application\Model\Item
     */
    public function getLite($id = null, $conversation_id = null)
    {
        return $this->getMapper()->select(
            $this->getModel()
                ->setId($id)
                ->setConversationId($conversation_id)
        );
    }

    /**
     * Update item
     *
     * @invokable
     *
     * @param  int    $id
     * @param  string $title
     * @param  int    $points
     * @param  string $description
     * @param  bool   $is_available
     * @param  bool   $is_published
     * @param  bool   $order
     * @param  string $start_date
     * @param  string $end_date
     * @param  int    $parent_id
     * @param  int    $library_id
     * @param  int    $post_id,
     * @param  string $text
     * @param  array  $participants,
     * @param  int    $quiz_id,
     * @param  int    $is_grade_published
     * @param  string $notify
     * @throws \Exception
     *
     * @return int
     */
    public function update($id, $title = null, $points = null, $description = null, $is_available = null, $is_published = null, $order = null, $start_date = null, $end_date = null, $parent_id = null, $library_id = null, $post_id = null, $text = null, $participants = null, $quiz_id = null, $is_grade_published = null, $notify = false)
    {
        $identity = $this->getServiceUser()->getIdentity();

        $m_item = $this->get($id);


        if (!$m_item || (!$this->getServiceUser()->isStudnetAdmin() && !$this->getServicePage()->isAdmin($m_item->getPageId())) ) {
            throw new JrpcException('Unauthorized operation item.update', -38003);
        }

        if(null !== $start_date) {
            $start_date = (new \DateTime($start_date))->format('Y-m-d H:i:s');
        }

        if(null !== $end_date) {
            $end_date = (new \DateTime($end_date))->format('Y-m-d H:i:s');
        }


        if (null !== $post_id) {
            $this->getServicePost()->_update($post_id, null, null, null, null, null, null, null, null, null, null, null, null, null, $id, null, false);
        }
        if (null !== $quiz_id) {
            $this->getServiceQuiz()->update($quiz_id, $id);
        }

        if($m_item->getIsPublished() != $is_published && $is_published!==null) {
            $this->publish($id, $is_published, null, null, $notify);


        } else if($notify === true && $m_item->getIsPublished()) {
            $m_page = $this->getServicePage()->getLite($m_item->getPageId());
            if($m_page->getIsPublished() == true) {
                $ar_pages = [];
                $m_item = $this->getLite($id)->current();
                $this->getServiceEvent()->create(
                    'item', 'update', 'ITMUPD'.$m_item->getId(),
                    ["PP".$m_page->getId()],
                    [
                        'item' => $id,
                        'page' => $m_page->getId(),
                        'page_type' => $m_page->getType(),
                        'picture' => !($m_page->getLogo() instanceof IsNull) ? $m_page->getLogo() : null
                    ],
                    [
                        'pagetitle' => $m_page->getTitle(),
                        'itemtitle' => $m_item->getTitle(),
                        'itemtype' => ModelItem::type_relation[$m_item->getType()],
                    ],
                    ['fcm' => Fcm::PACKAGE_TWIC_APP, 'mail' => true]
                );
            }
        }

        $m_updateditem = $this->getModel()
            ->setId($id)
            ->setTitle($title)
            ->setDescription($description)
            ->setIsAvailable($is_available)
            ->setPoints($points)
            ->setOrder($order)
            ->setLibraryId($library_id)
            ->setText($text)
            ->setIsGradePublished($is_grade_published)
            ->setParticipants($participants)
            ->setStartDate($start_date)
            ->setEndDate($end_date)
            ->setUpdatedDate((new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s'))
            ->setParentId($parent_id);

        if($m_item->getType() === ModelItem::TYPE_LIVE_CLASS ) {
            if((is_string($start_date) || is_string($m_item->getStartDate()))
                && ($is_published === 1 || ($is_published === null && $m_item->getIsPublished()))
            ) {
                $this->register($id, is_string($start_date) ? $start_date : $m_item->getStartDate());
            }
            else{
                $this->unregister($id);
            }
        }
        return $this->getMapper()->update($m_updateditem);
    }

    /**
     * Delete Item
     *
     * @invokable
     *
     * @param int $id
     */
    public function delete($id)
    {
        $identity = $this->getServiceUser()->getIdentity();

        $m_item = $this->get($id);
        if (!$m_item || (!$this->getServiceUser()->isStudnetAdmin() && !$this->getServicePage()->isAdmin($m_item->getPageId()))) {
            throw new JrpcException('Unauthorized operation item.delete', -38003);
        }

        if($m_item->getType() === ModelItem::TYPE_LIVE_CLASS) {
            $this->unregister($id);
        }

        return $this->getMapper()->delete(
            $this->getModel()
                ->setId($id)
        );
    }


    /**
     * Send Message Node item.register
     *
     * @param int $id
     */
    public function register($id, $date)
    {
        $rep = false;
        try {
            $data =   [
                'date' => $date,
                'uid' => 'item.starting.'.$id,
                'data' => [ 'type' => 'item.starting',
                            'data' => ['id' => $id]]
            ];
            $rep = $this->getServiceEvent()->nodeRequest('notification.register', $data);
            if ($rep->isError()) {
                throw new \Exception('Error jrpc nodeJs: ' . $rep->getError()->getMessage(), $rep->getError()->getCode());
            }
        } catch (\Exception $e) {
            syslog(1, 'Request notification.register : ' . json_encode($data));
            syslog(1, $e->getMessage());
        }

        return $rep;
    }

      /**
       * Send Message Node item.register
       *
       * @param int $id
       */
    public function unregister($id)
    {
        $rep = false;
        try {
            $data =  ['uid' => 'item.starting.'.$id];
            $rep = $this->getServiceEvent()->nodeRequest('notification.unregister', $data);
            if ($rep->isError()) {
                throw new \Exception('Error jrpc nodeJs: ' . $rep->getError()->getMessage(), $rep->getError()->getCode());
            }
        } catch (\Exception $e) {
            syslog(1, 'Request notification.unregister : ' . json_encode($data));
            syslog(1, $e->getMessage());
        }

        return $rep;
    }


    /**
     * Update item
     *
     * @invokable
     *
     * @param  int $id
     * @throws \Exception
     *
     * @return int
     */
    public function starting($id)
    {

        if(!is_array($id)) {
            $id = [$id];
        }
        $fcm_service = $this->getServiceFcm();
        foreach($id as $i){
            $m_item = $this->getLite($i)->current();
            $ar_user = ($m_item->getParticipants() === 'all') ?
                    $this->getServicePageUser()->getListByPage($m_item->getPageId())[$m_item->getPageId()] :
                    $this->getServiceItemUser()->getListUserId(null, $m_item->getId());

            $gcm_notification = new GcmNotification();
            $gcm_notification->setTitle($m_item->getTitle())
                ->setSound("default")
                ->setColor("#00A38B")
                ->setIcon("icon")
                ->setTag("LC".$m_item->getId())
                ->setBody('The Liveclass is starting now. Join!');

            $this->getServiceFcm()->send(
                $ar_user,
                ['data' => [
                    'type' => 'item.starting',
                    'data' => [
                        'id' => $i,
                        'title' => $m_item->getTitle(),
                        'start' => $m_item->getStartDate(),
                        'conversation_id' => $m_item->getConversationId(),
                        'type' => $m_item->getType()
                        ]
                    ]
                ],
                $gcm_notification,
                $fcm_service::PACKAGE_TWIC_APP
            );
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
     * Get Service Conversation
     *
     * @return \Application\Service\Conversation
     */
    private function getServiceConversation()
    {
        return $this->container->get('app_service_conversation');
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
     * Get Service Event
     *
     * @return \Application\Service\Event
     */
    private function getServiceEvent()
    {
        return $this->container->get('app_service_event');
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
     * Get Service Mail.
     *
     * @return \Mail\Service\Mail
     */
    private function getServiceMail()
    {
        return $this->container->get('mail.service');
    }

    /**
     * Get Service Item User
     *
     * @return \Application\Service\ItemUser
     */
    private function getServiceItemUser()
    {
        return $this->container->get('app_service_item_user');
    }

    /**
     * Get Service Group
     *
     * @return \Application\Service\Group
     */
    private function getServiceGroup()
    {
        return $this->container->get('app_service_group');
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
     * Get Service Quiz Answer
     *
     * @return \Application\Service\Quiz
     */
    private function getServiceQuiz()
    {
        return $this->container->get('app_service_quiz');
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

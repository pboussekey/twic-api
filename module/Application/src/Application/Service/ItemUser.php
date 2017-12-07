<?php
namespace Application\Service;

use Dal\Service\AbstractService;
use Zend\Db\Sql\Predicate\IsNull;

class ItemUser extends AbstractService
{

    /**
     * GetList User of Item
     *
     * @param int $item_id
     * @param int $user_id
     * @param int $submission_id
     *
     */
    public function getList($item_id, $user_id = null, $submission_id = null)
    {
        return $this->getMapper()->getList($item_id, $user_id, $submission_id);
    }
    
    /**
     * Get List User Id By group
     *
     * @param int $group_id
     * @param int $item_id
     */
    public function getListUserId($group_id = null, $item_id = null)
    {
        $res_item_user = $this->getMapper()->getListUserId($group_id, $item_id);
        
        $ret = [];
        foreach ($res_item_user as $m_item_user) {
            $ret[] = $m_item_user->getUserId();
        }
        
        return $ret;
    }

    /**
     * Get Item User
     *
     * @param int $id
     * @param int $user_id
     * @param int $submission_id
     * @param int $group_id
     * @param int $item_id
     *
     * @return \Application\Model\ItemUser
     *
     */
    public function getLite($id = null, $user_id = null, $submission_id = null, $group_id = null, $item_id = null)
    {
        return $this->getMapper()->select($this->getModel()
            ->setItemId($item_id)
            ->setId($id)
            ->setSubmissionId($submission_id)
            ->setUserId($user_id)
            ->setGroupId($group_id));
    }

    /**
     *
     * @param int $id
     * @param int $submission_id
     *
     * @return int
     */
    public function update($id, $submission_id)
    {
        $m_item_user = $this->getModel()
            ->setId($id)
            ->setSubmissionId($submission_id);
        
        return $this->getMapper()->update($m_item_user);
    }

    /**
     * GetList Item User or Create
     *
     * @param int $user_id
     * @param int $item_id
     * @param int $submission_id
     *
     * @return \Application\Model\ItemUser
     */
    public function getOrCreate($item_id, $user_id = null, $submission_id = null, $group_id = null)
    {
        $res_item_user = $this->getMapper()->select($this->getModel()
            ->setUserId($user_id)
            ->setItemId($item_id)
            ->setGroupId($group_id));
        
        if ($res_item_user->count() <= 0) {
            if ($user_id === null) {
                throw new \Exception("Error process: item_user is not présent");
            }
            $it_id = $this->create($item_id, $user_id, null, $submission_id);
            $res_item_user = $this->getMapper()->select($this->getModel()->setId($it_id));
        }

        $m_final_item_user = $res_item_user->current();
        
        if (null !== $submission_id && null !== $group_id) {
            $res_upt_item_user = $this->getMapper()->select($this->getModel()
                ->setItemId($item_id)
                ->setGroupId($group_id));
            
            foreach ($res_upt_item_user as $m_item_user) {
                if (null !== $submission_id && $m_item_user->getSubmissionId() !== $submission_id) {
                    $this->getMapper()->update($this->getModel()
                        ->setId($m_item_user->getId())
                        ->setSubmissionId($submission_id));
                }
            }
        } elseif(null !== $submission_id && $m_final_item_user->getSubmissionId() !== $submission_id) {
            $this->getMapper()->update($this->getModel()
                ->setId($m_final_item_user->getId())
                ->setSubmissionId($submission_id));
        }
        
        return $m_final_item_user;
    }

    /**
     * Create Item User
     *
     * @param int $item_id
     * @param int $user_id
     * @param int $group_id
     * @param int $submission_id
     *
     * @return \Application\Model\ItemUser
     */
    public function create($item_id, $user_id, $group_id = null, $submission_id = null)
    {
        $this->getMapper()->insert($this->getModel()
            ->setUserId($user_id)
            ->setItemId($item_id)
            ->setSubmissionId($submission_id)
            ->setGroupId($group_id));
        
        return (int) $this->getMapper()->getLastInsertValue();
    }

    /**
     * Add User In Item
     *
     * @param int $item_id
     * @param int|array $user_id
     */
    public function addUsers($item_id, $user_id, $group_id = null)
    {
        if (! is_array($user_id)) {
            $user_id = [
                $user_id
            ];
        }
        
        $res_item_user = $this->getMapper()->select($this->getModel()
            ->setUserId($user_id)
            ->setItemId($item_id));
        foreach ($res_item_user as $m_item_user) {
            //@TODO update submission_id
            $this->getMapper()->update($this->getModel()
                ->setGroupId($group_id)
                ->setDeletedDate(new IsNull('deleted_date')), [
                'id' => $m_item_user->getId()
            ]);
            unset($user_id[array_search($m_item_user->getUserId(), $user_id)]);
        }
        
        foreach ($user_id as $user) {
            $this->getMapper()->insert($this->getModel()
                ->setUserId($user)
                ->setGroupId($group_id)
                ->setItemId($item_id));
        }
        
        return true;
    }

    /**
     * Delete User In Item
     *
     * @param int $item_id
     * @param int|array $user_id
     */
    public function deleteUsers($item_id, $user_id)
    {
        if (! is_array($user_id)) {
            $user_id = [
                $user_id
            ];
        }
        
        $m_item_user = $this->getModel()->setDeletedDate((new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s'));
        foreach ($user_id as $user) {
            $this->getMapper()->update($m_item_user, [
                'user_id' => $user,
                'item_id' => $item_id
            ]);
        }
        
        return true;
    }

    public function grade($item_id, $rate, $user_id = null, $group_id = null)
    {
        $page_id = $this->getServiceItem()
            ->getLite($item_id)
            ->current()
            ->getPageId();
        $ar_pu = $this->getServicePageUser()->getListByPage($page_id, 'admin');
        $identity = $this->getServiceUser()->getIdentity();
        if (! in_array($identity['id'], $ar_pu[$page_id])) {
            throw new \Exception("No admin", 1);
        }
        
        return $this->_grade($item_id, $rate, $user_id, $group_id);
    }

    public function _grade($item_id, $rate, $user_id = null, $group_id = null)
    {
        if ($user_id !== null) {
            if (! is_array($user_id)) {
                $user_id = [
                    $user_id
                ];
            }
            foreach ($user_id as $user) {
                $res_item_user = $this->getMapper()->select($this->getModel()
                    ->setUserId($user)
                    ->setItemId($item_id));
                if ($res_item_user->count() > 0) {
                    $this->getMapper()->update($this->getModel()
                        ->setId($res_item_user->current()
                        ->getId())
                        ->setRate(($rate === - 1 ? new IsNull('grade') : $rate)));
                } else {
                    $this->getMapper()->insert($this->getModel()
                        ->setUserId($user)
                        ->setItemId($item_id)
                        ->setRate(($rate === - 1 ? new IsNull('grade') : $rate)));
                }
            }
        }
        
        if ($group_id !== null) {
            if (! is_array($group_id)) {
                $group_id = [
                    $group_id
                ];
            }
            foreach ($group_id as $group) {
                $this->getMapper()->update($this->getModel()
                    ->setRate($rate), [
                    'group_id' => $group
                ]);
            }
        }
        
        return true;
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
     * Get Service Item
     *
     * @return \Application\Service\Item
     */
    private function getServiceItem()
    {
        return $this->container->get('app_service_item');
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
}

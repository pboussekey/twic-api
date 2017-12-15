<?php

namespace Application\Service;

use Dal\Service\AbstractService;
use Application\Model\Conversation as ModelConversation;
use Zend\Db\Sql\Predicate\IsNull;

class ConversationUser extends AbstractService
{
    /**
     * Get Conversation if exist.
     *
     * @param array $users
     *
     * @return int|bool
     */
    public function getConversationIDByUser(array $users)
    {
        $user_id = $this->getServiceUser()->getIdentity()['id'];
        if (!in_array($user_id, $users)) {
            $users[] = $user_id;
        }

        $res_conversation_user = $this->getMapper()->getConversationIDByUser($users, ModelConversation::TYPE_CHAT);

        return ($res_conversation_user->count() === 1) ?
        $res_conversation_user->current()->getConversationId() : false;
    }

    /**
     * Add User in the Conversation.
     *
     * @param int       $conversation_id
     * @param int|array $users
     *
     * @return array
     */
    public function add($conversation_id, $users)
    {
        if (!is_array($users)) {
            $users = [$users];
        }

        $ret = [];
        foreach ($users as $user_id) {
            $ret[$user_id] = $this->getMapper()->add($conversation_id, $user_id);
        }

        return $ret;
    }

    /**
     * Mark Read Message(s).
     *
     * @invokable
     *
     * @param $conversation_id
     *
     * @return int
     */
    public function read($conversation_id)
    {
        $user_id = $this->getServiceUser()->getIdentity()['id'];

        $m_conversation_user = $this->getModel()
            ->setConversationId($conversation_id)
            ->setReadDate(new IsNull('read_date'))
            ->setUserId($user_id);

        return $this->getMapper()->update($m_conversation_user);
    }

    /**
     * Mark No Read Message(s).
     *
     * @invokable
     *
     * @param $conversation_id
     *
     * @return int
     */
    public function noread($conversation_id)
    {
        $ret = $this->getMapper()->update(
            $this->getModel()->setReadDate((new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s')),
            ['conversation_id' => $conversation_id]
        );

        $this->read($conversation_id);

        return $ret;
    }

    /**
     * DELETE User in the Conversation.
     *
     * @param int       $conversation_id
     * @param int|array $users
     *
     * @return array
     */
    public function delete($conversation_id, $users)
    {
        if (!is_array($users)) {
            $users = [$users];
        }

        $m_conversation_user = $this->getModel()->setConversationId($conversation_id);
        $ret = [];
        foreach ($users as $user_id) {
            $ret[$user_id] = $this->getMapper()->delete($m_conversation_user->setUserId($user_id));
        }

        return $ret;
    }


    /**
     * Get User By Conversation.
     *
     * @param int $conversation_id
     *
     * @return \Dal\Db\ResultSet\ResultSet
     */
    public function getListUserIdByConversation($conversation_id)
    {
        $ret = [];
        $res_conversation_user = $this->getMapper()->select($this->getModel()->setConversationId($conversation_id));
        foreach ($res_conversation_user as $m_conversation_user) {
            $ret[] = $m_conversation_user->getUserId();
        }

        return $ret;
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
}

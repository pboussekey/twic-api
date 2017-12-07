<?php

namespace Application\Service;

use Dal\Service\AbstractService;
use Zend\Db\Sql\Predicate\IsNull;

class MessageUser extends AbstractService
{

  /**
   * Send message.
   *
   * @param int      $message_id
   * @param int      $conversation_id
   * @param string   $message_text
   * @param string   $message_token
   *
   * @throws \Exception
   *
   * @return int
   */
    public function send($message_id, $conversation_id, $message_text, $message_token)
    {
        $me = $this->getServiceUser()->getIdentity()['id'];

        $for_me = false;
        $to = $this->getServiceConversationUser()->getListUserIdByConversation($conversation_id);

        $date = (new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s');
        foreach ($to as $user) {
            $m_message_user = $this->getModel()
              ->setMessageId($message_id)
              ->setConversationId($conversation_id)
              ->setFromId($me)
              ->setUserId($user)
              ->setType((($user == $me) ? (($for_me) ? 'RS' : 'S') : 'R'))
              ->setCreatedDate($date);

            if ($me == $user && !$for_me) {
                $m_message_user->setReadDate((new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s'));
            }

            if ($this->getMapper()->insert($m_message_user) <= 0) {
                throw new \Exception('error insert message to');
            }
        }

        return $this->getMapper()->getLastInsertValue();
    }

    /**
     * Mark read Message User by conversation.
     *
     * @param int|array $conversation_id
     *
     * @return int
     */
    public function readByConversation($conversation_id)
    {
        $user_id = $this->getServiceUser()->getIdentity()['id'];

        if (!is_array($conversation_id)) {
            $conversation_id = [$conversation_id];
        }

        $m_message_user = $this->getModel()->setReadDate((new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s'));

        return $this->getMapper()->update($m_message_user, ['conversation_id' => $conversation_id, 'user_id' => $user_id, new IsNull('read_date')]);
    }

    /**
     * Get Service Service User.
     *
     * @return \Application\Service\User
     */
    private function getServiceUser()
    {
        return $this->container->get('app_service_user');
    }

    /**
     * Get Service Service Conversation User.
     *
     * @return \Application\Service\ConversationUser
     */
    private function getServiceConversationUser()
    {
        return $this->container->get('app_service_conversation_user');
    }
}

<?php

namespace Application\Service;

use Dal\Service\AbstractService;

class Report extends AbstractService
{
    /**
     * Create Report.
     *
     * @invokable
     *
     * @param int    $user_id
     * @param int    $post_id
     * @param int    $comment_id
     * @param string $reason
     * @param string $description
     *
     * @return int
     */
    public function add($reason, $description = null, $user_id = null, $post_id = null, $page_id = null)
    {
        $identity = $this->getServiceUser()->getIdentity();

        $m_report = $this->getModel()
          ->setReporterId($identity['id'])
          ->setUserId($user_id)
          ->setPostId($post_id)
          ->setPageId($page_id);

        if ($this->getMapper()->select($m_report)->count() > 0) {
            throw new \Exception('Duplicate report');
        }
        $m_report->setReason($reason)
          ->setDescription($description)
          ->setCreatedDate((new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s'));

        if ($this->getMapper()->insert($m_report) <= 0) {
            throw new \Exception('Error during report');
        }

        return (int)$this->getMapper()->getLastInsertValue();
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

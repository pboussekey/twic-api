<?php
namespace Application\Service;

use Dal\Service\AbstractService;
use Application\Model\Page as ModelPage;
use ZendService\Google\Gcm\Notification as GcmNotification;
use Zend\Db\Sql\Predicate\IsNull;
use Google\Cloud\Logging\LoggingClient;
use Application\Service\Event as ModelEvent;

class PageDoc extends AbstractService
{

    /**
     * Add Page Document Relation
     *
     * @param int $page_id
     * @param int|array $library
     * @param bool true
     * @return int
     */
    public function add($page_id, $library, $notify=true)
    {
        if (is_array($library)) {
            $m_library = $this->getServiceLibrary()
                ->_add($library);

            $library_id = $m_library->getId();
        } elseif (! is_numeric($var)) {
            throw new \Exception('error add document');
        }

        $m_page_doc = $this->getModel()
            ->setPageId($page_id)
            ->setLibraryId($library_id);

        $this->getMapper()->insert($m_page_doc);
        $m_page = $this->getServicePage()->getLite($page_id);
        if ($m_page->getType() == ModelPage::TYPE_COURSE && $notify===true) {
            $this->getServiceEvent()->create(
                'page','doc',
                ["PP".$page_id],
                [
                    'picture' => !($m_page->getLogo() instanceof IsNull) ? $m_page->getLogo() : null,
                    'page'    => $page_id,
                    'library'    => $library_id,
                    'page_type' => $m_page->getType(),
                ],
                [
                    'page_title' => $m_page->getTitle(),
                    'library_name' => $m_library->getName(),
                ],
                ['fcm' => Fcm::PACKAGE_TWIC_APP, 'mail' => true]
            );
        }
        return $library;
    }

    /**
     * Delete Doc
     *
     * @param int $library_id
     */
    public function delete($library_id)
    {
        $this->getMapper()->delete($this->getModel()
            ->setLibraryId($library_id));

        return $this->getServiceLibrary()->delete($library_id);
    }

    /**
     * Add Array
     *
     * @param int $page_id
     * @param array $data
     * @return array
     */
    public function _add($page_id, $data)
    {
        $ret = [];
        foreach ($data as $d) {
            $ret[] = $this->add($page_id, $d);
        }

        return $ret;
    }

    /**
     * Replace Array
     *
     * @param int $page_id
     * @param array $data
     * @return array
     */
    public function replace($page_id, $data)
    {
        $this->getMapper()->delete($this->getModel()
            ->setPageId($page_id));

        return $this->_add($page_id, $data);
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
     * Get Service Event
     *
     * @return \Application\Service\Event
     */
    private function getServiceEvent()
    {
        return $this->container->get('app_service_event');
    }

    /**
     * Get Service Page User
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
     * Get Service Mail.
     *
     * @return \Mail\Service\Mail
     */
    private function getServiceMail()
    {
        return $this->container->get('mail.service');
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
     * Get Service Service Conversation User.
     *
     * @return \Application\Service\Fcm
     */
    private function getServiceFcm()
    {
        return $this->container->get('fcm');
    }
}

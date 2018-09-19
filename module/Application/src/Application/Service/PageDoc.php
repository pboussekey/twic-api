<?php
namespace Application\Service;

use Dal\Service\AbstractService;
use Application\Model\Page as ModelPage;
use ZendService\Google\Gcm\Notification as GcmNotification;
use Zend\Db\Sql\Predicate\IsNull;

class PageDoc extends AbstractService
{
    /**
     * Add Page Document Relation
     *
     * @param  int       $page_id
     * @param  int|array $library
     * @return int
     */
    public function add($page_id, $library)
    {
        if (is_array($library)) {
            $library = $this->getServiceLibrary()->_add($library)->getId();
        } elseif (!is_numeric($var)) {
            throw new \Exception('error add document');
        }

        $m_page_doc = $this->getModel()
            ->setPageId($page_id)
            ->setLibraryId($library);

        $this->getMapper()->insert($m_page_doc);

        $m_page = $this->getServicePage()->getLite($page_id);
        if($m_page->getType() == ModelPage::TYPE_COURSE) {
            $identity = $this->getServiceUser()->getIdentity();
            $ar_pages = [];
            $res_user = $this->getServiceUser()->getLite($this->getServicePageUser()->getListByPage($page_id)[$page_id]);
            if($res_user !== null) {
                foreach($res_user as $m_user){
                    $m_organization = false;
                    if(!$m_user->getOrganizationId() instanceof IsNull) {
                        if(!array_key_exists($m_user->getOrganizationId(), $ar_pages)) {
                            $ar_pages[$m_user->getOrganizationId()] = $this->getServicePage()->getLite($m_user->getOrganizationId());
                        }
                        $m_organization = $ar_pages[$m_user->getOrganizationId()];
                    }

                    try{
                        if($m_user->getId() == $identity['id'] && $m_user->getHasEmailNotifier() === 1) {
                            $prefix = ($m_organization !== false && is_string($m_organization->getLibelle()) && !empty($m_organization->getLibelle())) ?
                                $m_organization->getLibelle() : null;
                            $url = sprintf(
                                "https://%s%s/page/course/%s/resources",
                                ($prefix ? $prefix.'.':''),
                                $this->container->get('config')['app-conf']['uiurl'],
                                $m_page->getId()
                            );
                            $this->getServiceMail()->sendTpl(
                                'tpl_coursedoc', $m_user->getEmail(), [
                                'pagename' => $m_page->getTitle(),
                                'firstname' => $m_user->getFirstName(),
                                'pageurl' => $url
                                ]
                            );
                        }

                        $gcm_notification = new GcmNotification();
                        $gcm_notification->setTitle($m_page->getTitle())
                            ->setSound("default")
                            ->setColor("#00A38B")
                            ->setIcon("icon")
                            ->setTag("PAGEDOV".$page_id)
                            ->setBody("A new material has been added to the course ". $m_page->getTitle());

                            $this->getServiceFcm()->send($m_user->getId(), null, $gcm_notification, Fcm::PACKAGE_TWIC_APP);
                    }
                    catch (\Exception $e) {
                        syslog(1, 'Model name does not exist PageDoc <MESSAGE> ' . $e->getMessage() . '  <CODE> ' . $e->getCode());
                    }
                }
            }
        }


        return $library;
    }


    /**
     * Delete Doc
     *
     * @param int $library_id
     **/
    public function delete($library_id)
    {
        $this->getMapper()->delete($this->getModel()->setLibraryId($library_id));

        return $this->getServiceLibrary()->delete($library_id);
    }

    /**
     * Add Array
     *
     * @param  int   $page_id
     * @param  array $data
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
     * @param  int   $page_id
     * @param  array $data
     * @return array
     */
    public function replace($page_id, $data)
    {
        $this->getMapper()->delete($this->getModel()->setPageId($page_id));

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

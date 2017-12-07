<?php
/**
 * TheStudnet (http://thestudnet.com).
 *
 * Resume
 */
namespace Application\Service;

use Dal\Service\AbstractService;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Predicate\IsNull;

/**
 * Class Resume.
 */
class Resume extends AbstractService
{
    /**
     * Add experience
     *
     * @invokable
     *
     * @param string $start_date
     * @param string $end_date
     * @param string $address
     * @param string $logo
     * @param string $title
     * @param string $subtitle
     * @param string $description
     * @param int    $type
     * @param string $publisher
     * @param string $url
     * @param string $cause
     * @param string $study
     * @param int    $grade
     * @param int    $note
     *
     * @throws \Exception
     *
     * @return int
     */
    public function add(
        $start_date = null,
        $end_date = null,
        $address = null,
        $logo = null,
        $title = null,
        $subtitle = null,
    $description = null,
        $type = null,
        $publisher = null,
        $url = null,
        $cause = null,
        $study = null,
        $grade = null,
        $note = null
    ) {
        $m_education = $this->getModel();

        if ($end_date === 'null') {
            $end_date = new IsNull();
        }
        if ($start_date === 'null') {
            $start_date = new IsNull();
        }

        $user_id = $this->getServiceUser()->getIdentity()['id'];
        $m_education->setAddress($address)
            ->setLogo($logo)
            ->setStartDate($start_date)
            ->setEndDate($end_date)
            ->setTitle($title)
            ->setSubtitle($subtitle)
            ->setDescription($description)
            ->setType($type)
            ->setPublisher($publisher)
            ->setUrl($url)
            ->setCause($cause)
            ->setStudy($study)
            ->setGrade($grade)
            ->setNote($note)
            ->setUserId($user_id);

        if ($this->getMapper()->insert($m_education) <= 0) {
            throw new \Exception('error insert experience');
        }

        return (int)$this->getMapper()->getLastInsertValue();
    }

    /**
     * Update experience.
     *
     * @invokable
     *
     * @param int    $id
     * @param string $start_date
     * @param string $end_date
     * @param string $address
     * @param string $logo
     * @param string $title
     * @param string $subtitle
     * @param string $description
     * @param int    $type
     * @param string $publisher
     * @param string $url
     * @param string $cause
     * @param string $study
     * @param int    $grade
     * @param int    $note
     *
     * @return int
     */
    public function update($id, $start_date = null, $end_date = null, $address = null, $logo = null, $title = null, $subtitle = null, $description = null, $type = null, $publisher = null, $url = null, $cause = null, $study = null, $grade = null, $note = null)
    {
        $m_education = $this->getModel();

        if ($end_date === 'null') {
            $end_date = new IsNull();
        }
        if ($start_date === 'null') {
            $start_date = new IsNull();
        }

        $user_id = $this->getServiceUser()->getIdentity()['id'];
        $m_education->setAddress($address)
            ->setLogo($logo)
            ->setStartDate($start_date)
            ->setEndDate($end_date)
            ->setTitle($title)
            ->setSubtitle($subtitle)
            ->setDescription($description)
            ->setType($type)
            ->setPublisher($publisher)
            ->setUrl($url)
            ->setCause($cause)
            ->setStudy($study)
            ->setGrade($grade)
            ->setNote($note)
            ->setUserId($user_id);

        return $this->getMapper()->update($m_education, ['id' => $id, 'user_id' => $user_id]);
    }

    /**
     * Update education experience.
     *
     * @invokable
     *
     * @param int $id
     *
     * @return int
     */
    public function delete($id)
    {
        $m_education  = $this->getModel()
          ->setId($id)
          ->setUserId($this->getServiceUser()->getIdentity()['id']);

        return $this->getMapper()->delete($m_education);
    }

    /**
     * Get Resume
     *
     * @invokable
     *
     * @param  int|array $id
     * @return array
     */
    public function get($id)
    {
        $res_resume = $this->getMapper()->select($this->getModel()->setId($id), [new Expression('ISNULL(end_date) DESC'), 'end_date DESC']);

        return (is_array($id)) ?
        $res_resume->toArray(['id']) :
        $res_resume->current();
    }




    /**
     * Get list resume id by users.
     *
     * @invokable
     *
     * @param int|array $id
     *
     * @return \Dal\Db\ResultSet\ResultSet
     */
    public function getListId($user_id)
    {
        if (!is_array($user_id)) {
            $user_id = [$user_id];
        }

        $resumes = [];
        foreach ($user_id as $user) {
            $resumes[$user] = [];
        }

        $res_resume = $this->getMapper()->getListId($user_id);
        foreach ($res_resume as $m_resume) {
            $resumes[$m_resume->getUserId()][] = $m_resume->getId();
        }

        return $resumes;
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

<?php

namespace Application\Service;

use Dal\Service\AbstractService;

class PostDoc extends AbstractService
{
    /**
     * Add Post Document Relation
     *
     * @invokable
     *
     * @param  int       $post_id
     * @param  int|array $library
     * @return int
     */
    public function add($post_id, $library)
    {
        if (is_array($library)) {
            $library = $this->getServiceLibrary()->_add($library)->getId();
        } elseif (!is_numeric($library)) {
            throw new \Exception('error add document');
        }

        $m_post_doc = $this->getModel()
            ->setPostId($post_id)
            ->setLibraryId($library);

        return $this->getMapper()->insert($m_post_doc);
    }

    /**
     * Add Array
     *
     * @param  int   $post_id
     * @param  array $data
     * @return array
     */
    public function _add($post_id, $data)
    {
        $ret = [];
        foreach ($data as $d) {
            $ret[] = $this->add($post_id, $d);
        }

        return $ret;
    }

    /**
     * Replace Array
     *
     * @param  int   $post_id
     * @param  array $data
     * @return array
     */
    public function replace($post_id, $data)
    {
        foreach ($this->getMapper()->select($this->getModel()->setPostId($post_id)) as $m_post_doc) {
            $this->getServiceLibrary()->delete($m_post_doc->getLibraryId());
        }
      
        $this->getMapper()->delete($this->getModel()->setPostId($post_id));

        return $this->_add($post_id, $data);
    }

    /**
     *Get List Post Doc
     *
     * @param  int $post_id
     * @return \Dal\Db\ResultSet\ResultSet
     */
    public function getList($post_id)
    {
        return $this->getServiceLibrary()->getListByPost($post_id);
    }

    /**
     *Get  Post Doc
     *
     * @param  int $id
     * @return \Dal\Db\ResultSet\ResultSet
     */
    public function get($id)
    {
        return $this->getMapper()->select($this->getModel()->setId($id));
    }


    /**
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
}

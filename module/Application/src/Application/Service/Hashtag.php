<?php

namespace Application\Service;

use Dal\Service\AbstractService;
use Zend\Db\Sql\Predicate\IsNotNull;

class Hashtag extends AbstractService
{
    public function add($name, $post_id)
    {
        if (!is_array($name)) {
            $name = [$name];
        }
        $m_hashtag = $this->getModel()->setPostId($post_id);
        $name = array_unique($name);
        foreach ($name as $n) {
            $m_hashtag->setName(substr($n, 1))
                ->setType(substr($n, 0, 1));

            if ($this->getMapper()->select($m_hashtag)->count() <= 0) {
                $m_hashtag->setCreatedDate((new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s'));
                $this->getMapper()->insert($m_hashtag);
            }
        }

        return true;
    }

    public function addMentions($id, $mentions)
    {
        $m_hashtag = $this->getModel()->setPostId($id);
        $user_id = [];
        $users = [];
        for ($i = 0; $i < count($mentions[0]); $i++) {
            $m_hashtag->setName($mentions[0][$i])
                ->setType('@')->setUserId($mentions[1][$i]);
            if ($this->getMapper()->select($m_hashtag)->count() <= 0) {
                $m_hashtag->setCreatedDate((new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s'));
                $this->getMapper()->insert($m_hashtag);
                $user_id[] = $mentions[1][$i];
            }
            $users[] = $mentions[1][$i];
        }
        $this->getMapper()->keepMentions($id, $users);
        return $user_id;
    }



    public function addHashtags($id, $hashtags)
    {
        $m_hashtag = $this->getModel()->setPostId($id);
        for ($i = 0; $i < count($hashtags[0]); $i++) {
            $m_hashtag->setName($hashtags[0][$i])
                ->setType('#');
            if ($this->getMapper()->select($m_hashtag)->count() <= 0) {
                $m_hashtag->setCreatedDate((new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s'));
                $this->getMapper()->insert($m_hashtag);
            }
        }
        $this->getMapper()->keepHashtags($id, $hashtags[0]);
        return true;
    }

    public function getListMentions($id)
    {
        $m_hashtag = $this->getModel()->setPostId($id)->setType('@')->setUserId(new IsNotNull());
        $res_hashtag = $this->getMapper()->select($m_hashtag);
        $ar_user = [];
        foreach($res_hashtag as $m_hashtag){
            $ar_user[] = $m_hashtag->getUserId();
        }
        return $ar_user;
    }

    public function getList($filter = [], $search)
    {
        if (strpos($search, '#') === 0 || strpos($search, '@') === 0) {
            $search = substr($search, 1);
        }

        $mapper = $this->getMapper();

        $res_hashtag = $mapper->usePaginator($filter)->getList($search);

        return ['count' => $mapper->count(), 'list' => $res_hashtag];
    }
}

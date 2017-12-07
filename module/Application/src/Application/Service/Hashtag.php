<?php

namespace Application\Service;

use Dal\Service\AbstractService;

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

    public function getList($filter = [], $search)
    {
        if (strpos($search, '#') === 0 || strpos($search, '@') === 0) {
            $search = substr($search, 1);
        }

        $mapper = $this->getMapper();

        $res_account = $mapper->usePaginator($filter)->getList($search);

        return ['count' => $mapper->count(), 'list' => $res_account];
    }
}

<?php

namespace Application\Service;

use Dal\Service\AbstractService;

class Language extends AbstractService
{
    /**
     * Get List.
     *
     * @invokable
     *
     * @param string $search
     * @param array $filter
     *
     * @return \Dal\Db\ResultSet\ResultSet
     */
    public function getList($search = null, $filter = null)
    {
        $mapper = $this->getMapper();
        $res_language =  $mapper->usePaginator($filter)->getList($search);
        return (null !== $filter) ?
          ['list' => $res_language,'count' => $mapper->count()] :
          $res_language;
    }
}

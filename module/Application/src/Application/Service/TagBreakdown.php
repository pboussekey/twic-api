<?php

namespace Application\Service;

use Dal\Service\AbstractService;

class TagBreakdown extends AbstractService
{

        public function create($tag_id, $name = null){
              if(null === $name){
                  $m_tag = $this->getServiceTag()->get($tag_id);
                  $name = $m_tag->getName();
              }
              $return = 0;
              $words = explode(' ', strtolower(trim(preg_replace('/([A-Z][a-z0-9])/',' ${0}', $name))));
              $words[] = strtolower(str_replace(' ', '', $name));
              $words = array_unique($words);
              foreach($words as $word){
                  $return += $this->getMapper()->insert($this->getModel()->setTagId($tag_id)->setTagPart(strtolower($word)));
              }
              return $return;
        }



        /**
       * Get Service User Tag
       *
       * @return \Application\Service\Tag
       */
        private function getServiceTag()
        {
            return $this->container->get('app_service_tag');
        }
}

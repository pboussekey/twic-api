<?php
/**
 * TheStudnet (http://thestudnet.com)
 *
 * UserTag
 */
namespace Application\Service;

use Dal\Service\AbstractService;

/**
 * Class UserTag
 */
class UserTag extends AbstractService
{

  /**
   * Add User Tag
   *
   * @param  int    $user_id
   * @param  string $tag
   * @param  string $category
   * @return int
   */
  public function add($user_id, $tag, $category = null)
  {
      $m_user_tag = $this->getModel()
          ->setUserId($user_id)
          ->setCategory($category)
          ->setTagId($this->getServiceTag()->add($tag));

      $this->getMapper()->insert($m_user_tag);

      return $m_user_tag->getTagId();
  }

  /**
   * Add Array
   *
   * @param  int   $user_id
   * @param  array $data
   * @return array
   */
  public function _add($user_id, $data, $category = null)
  {
      $ret = [];
      foreach ($data as $tag) {
          $ret = $this->add($user_id, $tag, $category);
      }

      return $ret;
  }

  /**
   * Replacec Array
   *
   * @param  int   $user_id
   * @param  array $data
   * @return array
   */
  public function replace($user_id, $data, $category = null)
  {
      $this->getMapper()->delete($this->getModel()->setUserId($user_id)->setCategory($category));

      return  $this->_add($user_id, $data, $category);
  }

  /**
   * Remove Tag
   *
   * @param  int $user_id
   * @param  int $tag_id
   * @param  string $category
   * @return bool
   */
  public function remove($user_id, $tag_id = null, $category = null)
  {
      return $this->getMapper()->delete(
          $this->getModel()->setUserId($user_id)->setTagId($tag_id)->setCategory($category)
      );
  }

  /**
   * Get List
   *
   * @param int $user_id
   */
  public function getList($user_id)
  {
      return $this->getServiceTag()->getListByUser($user_id);
  }

  /**
   *
   * @return \Application\Service\Tag
   */
  private function getServiceTag()
  {
      return $this->container->get('app_service_tag');
  }
}

<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class Post extends AbstractModel
{
    protected $id;
    protected $content;
    protected $user_id;
    protected $page_id;
    protected $link;
    protected $picture;
    protected $name_picture;
    protected $link_title;
    protected $link_desc;
    protected $created_date;
    protected $deleted_date;
    protected $updated_date;
    protected $parent_id;
    protected $origin_id;
    protected $t_page_id;
    protected $t_user_id;
    protected $t_course_id;
    protected $type;
    protected $data;
    protected $lat;
    protected $lng;
    protected $uid;
    protected $item_id;

    protected $prefix = 'post';

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getPageId()
    {
        return $this->page_id;
    }

    public function setPageId($page_id)
    {
        $this->page_id = $page_id;

        return $this;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    public function getPicture()
    {
        return $this->picture;
    }

    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

    public function getNamePicture()
    {
        return $this->name_picture;
    }

    public function setNamePicture($name_picture)
    {
        $this->name_picture = $name_picture;

        return $this;
    }

    public function getLinkTitle()
    {
        return $this->link_title;
    }

    public function setLinkTitle($link_title)
    {
        $this->link_title = $link_title;

        return $this;
    }

    public function getLinkDesc()
    {
        return $this->link_desc;
    }

    public function setLinkDesc($link_desc)
    {
        $this->link_desc = $link_desc;

        return $this;
    }

    public function getCreatedDate()
    {
        return $this->created_date;
    }

    public function setCreatedDate($created_date)
    {
        $this->created_date = $created_date;

        return $this;
    }

    public function getDeletedDate()
    {
        return $this->deleted_date;
    }

    public function setDeletedDate($deleted_date)
    {
        $this->deleted_date = $deleted_date;

        return $this;
    }

    public function getUpdatedDate()
    {
        return $this->updated_date;
    }

    public function setUpdatedDate($updated_date)
    {
        $this->updated_date = $updated_date;

        return $this;
    }

    public function getParentId()
    {
        return $this->parent_id;
    }

    public function setParentId($parent_id)
    {
        $this->parent_id = $parent_id;

        return $this;
    }

    public function getOriginId()
    {
        return $this->origin_id;
    }

    public function setOriginId($origin_id)
    {
        $this->origin_id = $origin_id;

        return $this;
    }

    public function getTPageId()
    {
        return $this->t_page_id;
    }

    public function setTPageId($t_page_id)
    {
        $this->t_page_id = $t_page_id;

        return $this;
    }

    public function getTUserId()
    {
        return $this->t_user_id;
    }

    public function setTUserId($t_user_id)
    {
        $this->t_user_id = $t_user_id;

        return $this;
    }

    public function getTCourseId()
    {
        return $this->t_course_id;
    }

    public function setTCourseId($t_course_id)
    {
        $this->t_course_id = $t_course_id;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    public function getLat()
    {
        return $this->lat;
    }

    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLng()
    {
        return $this->lng;
    }

    public function setLng($lng)
    {
        $this->lng = $lng;

        return $this;
    }

    public function getUid()
    {
        return $this->uid;
    }

    public function setUid($uid)
    {
        $this->uid = $uid;

        return $this;
    }

    public function getItemId()
    {
        return $this->item_id;
    }

    public function setItemId($item_id)
    {
        $this->item_id = $item_id;

        return $this;
    }
}

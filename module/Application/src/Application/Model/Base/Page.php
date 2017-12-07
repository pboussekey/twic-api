<?php

namespace Application\Model\Base;

use Dal\Model\AbstractModel;

class Page extends AbstractModel
{
    protected $id;
    protected $title;
    protected $logo;
    protected $background;
    protected $description;
    protected $confidentiality;
    protected $admission;
    protected $start_date;
    protected $end_date;
    protected $location;
    protected $type;
    protected $user_id;
    protected $address_id;
    protected $deleted_date;
    protected $owner_id;
    protected $uid;
    protected $short_title;
    protected $website;
    protected $phone;
    protected $libelle;
    protected $custom;
    protected $subtype;
    protected $conversation_id;
    protected $created_date;
    protected $is_published;

    protected $prefix = 'page';

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getLogo()
    {
        return $this->logo;
    }

    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    public function getBackground()
    {
        return $this->background;
    }

    public function setBackground($background)
    {
        $this->background = $background;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getConfidentiality()
    {
        return $this->confidentiality;
    }

    public function setConfidentiality($confidentiality)
    {
        $this->confidentiality = $confidentiality;

        return $this;
    }

    public function getAdmission()
    {
        return $this->admission;
    }

    public function setAdmission($admission)
    {
        $this->admission = $admission;

        return $this;
    }

    public function getStartDate()
    {
        return $this->start_date;
    }

    public function setStartDate($start_date)
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function getEndDate()
    {
        return $this->end_date;
    }

    public function setEndDate($end_date)
    {
        $this->end_date = $end_date;

        return $this;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function setLocation($location)
    {
        $this->location = $location;

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

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getAddressId()
    {
        return $this->address_id;
    }

    public function setAddressId($address_id)
    {
        $this->address_id = $address_id;

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

    public function getOwnerId()
    {
        return $this->owner_id;
    }

    public function setOwnerId($owner_id)
    {
        $this->owner_id = $owner_id;

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

    public function getShortTitle()
    {
        return $this->short_title;
    }

    public function setShortTitle($short_title)
    {
        $this->short_title = $short_title;

        return $this;
    }

    public function getWebsite()
    {
        return $this->website;
    }

    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    public function getLibelle()
    {
        return $this->libelle;
    }

    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getCustom()
    {
        return $this->custom;
    }

    public function setCustom($custom)
    {
        $this->custom = $custom;

        return $this;
    }

    public function getSubtype()
    {
        return $this->subtype;
    }

    public function setSubtype($subtype)
    {
        $this->subtype = $subtype;

        return $this;
    }

    public function getConversationId()
    {
        return $this->conversation_id;
    }

    public function setConversationId($conversation_id)
    {
        $this->conversation_id = $conversation_id;

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

    public function getIsPublished()
    {
        return $this->is_published;
    }

    public function setIsPublished($is_published)
    {
        $this->is_published = $is_published;

        return $this;
    }
}

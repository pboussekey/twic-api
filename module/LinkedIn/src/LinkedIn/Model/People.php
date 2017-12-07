<?php
namespace LinkedIn\Model;

class People extends AbstractModel
{
    protected $id;
    protected $first_name;
    protected $last_name;
    protected $maiden_name;
    protected $formatted_name;
    protected $phonetic_first_name;
    protected $phonetic_last_name;
    protected $formatted_phonetic_name;
    protected $headline;
    protected $location;
    protected $industry;
    protected $current_share;
    protected $num_connections;
    protected $num_connections_capped;
    protected $summary;
    protected $specialties;
    protected $positions;
    protected $picture_url;
    protected $picture_urls;
    protected $site_standard_profile_request;
    protected $api_standard_profile_request;
    protected $public_profile_url;
    
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->first_name;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->last_name;
    }

    /**
     * @return string
     */
    public function getMaidenName()
    {
        return $this->maiden_name;
    }

    /**
     * @return string 
     */
    public function getFormattedName()
    {
        return $this->formatted_name;
    }

    /**
     * @return string 
     */
    public function getPhoneticFirstname()
    {
        return $this->phonetic_first_name;
    }

    /**
     * @return string 
     */
    public function getPhoneticLastname()
    {
        return $this->phonetic_last_name;
    }

    /**
     * @return string 
     */
    public function getFormattedPhoneticName()
    {
        return $this->formatted_phonetic_name;
    }

    /**
     * @return string 
     */
    public function getHeadline()
    {
        return $this->headline;
    }

    /**
     * @return string 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @return string 
     */
    public function getIndustry()
    {
        return $this->industry;
    }

    /**
     * @return string $current_share
     */
    public function getCurrentShare()
    {
        return $this->current_share;
    }

    /**
     * @return string
     */
    public function getNumConnections()
    {
        return $this->num_connections;
    }

    /**
     * @return string
     */
    public function getNumConnectionsCapped()
    {
        return $this->num_connections_capped;
    }

    /**
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @return string
     */
    public function getSpecialties()
    {
        return $this->specialties;
    }

    /**
     * @return int
     */
    public function getPositions()
    {
        return $this->positions;
    }

    /**
     * @return string
     */
    public function getPictureUrl()
    {
        return $this->picture_url;
    }

    /**
     * @return string
     */
    public function getPictureUrls()
    {
        return $this->picture_urls;
    }

    /**
     * @return string
     */
    public function getSiteStandardProfileRequest()
    {
        return $this->site_standard_profile_request;
    }

    /**
     * @return string
     */
    public function getApiStandardProfileRequest()
    {
        return $this->api_standard_profile_request;
    }

    /**
     * @return string
     */
    public function getPublicProfileUrl()
    {
        return $this->public_profile_url;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
        
        return $this;
    }

    /**
     * @param string $first_name
     */
    public function setFirstname($first_name)
    {
        $this->first_name = $first_name;
        
        return $this;
    }

    /**
     * @param string $last_name
     */
    public function setLastname($last_name)
    {
        $this->last_name = $last_name;
        
        return $this;
    }

    /**
     * @param string $maiden_name
     */
    public function setMaidenName($maiden_name)
    {
        $this->maiden_name = $maiden_name;
        
        return $this;
    }

    /**
     * @param string $formatted_name
     */
    public function setFormattedName($formatted_name)
    {
        $this->formatted_name = $formatted_name;
        
        return $this;
    }

    /**
     * @param string $phonetic_first_name
     */
    public function setPhoneticFirstname($phonetic_first_name)
    {
        $this->phonetic_first_name = $phonetic_first_name;
        
        return $this;
    }

    /**
     * @param string $phonetic_last_name
     */
    public function setPhoneticLastname($phonetic_last_name)
    {
        $this->phonetic_last_name = $phonetic_last_name;
        
        return $this;
    }

    /**
     * @param string $formatted_phonetic_name
     */
    public function setFormattedPhoneticName($formatted_phonetic_name)
    {
        $this->formatted_phonetic_name = $formatted_phonetic_name;
        
        return $this;
    }

    /**
     * @param string $headline
     */
    public function setHeadline($headline)
    {
        $this->headline = $headline;
        
        return $this;
    }

    /**
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
        
        return $this;
    }

    /**
     * @param string $industry
     */
    public function setIndustry($industry)
    {
        $this->industry = $industry;
        
        return $this;
    }

    /**
     * @param string $current_share
     */
    public function setCurrentShare($current_share)
    {
        $this->current_share = $current_share;
        
        return $this;
    }

    /**
     * @param string $num_connections
     */
    public function setNumConnections($num_connections)
    {
        $this->num_connections = $num_connections;
        
        return $this;
    }

    /**
     * @param string $num_connections_capped
     */
    public function setNumConnectionsCapped($num_connections_capped)
    {
        $this->num_connections_capped = $num_connections_capped;
        
        return $this;
    }

    /**
     * @param string $summary
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
        
        return $this;
    }

    /**
     * @param string $specialties
     */
    public function setSpecialties($specialties)
    {
        $this->specialties = $specialties;
        
        return $this;
    }

    /**
     * @param string $positions
     */
    public function setPositions($positions)
    {
        $this->positions = $positions;
        
        return $this;
    }

    /**
     * @param string $picture_url
     */
    public function setPictureUrl($picture_url)
    {
        $this->picture_url = $picture_url;
        
        return $this;
    }

    /**
     * @param string $picture_urls
     */
    public function setPictureUrls($picture_urls)
    {
        $this->picture_urls = $picture_urls;
        
        return $this;
    }

    /**
     * @param string $site_standard_profile_request
     */
    public function setSiteStandardProfileRequest($site_standard_profile_request)
    {
        $this->site_standard_profile_request = $site_standard_profile_request;
        
        return $this;
    }

    /**
     * @param string $api_standard_profile_request
     */
    public function setApiStandardProfileRequest($api_standard_profile_request)
    {
        $this->api_standard_profile_request = $api_standard_profile_request;
        
        return $this;
    }

    /**
     * @param string $public_profile_url
     */
    public function setPublicProfileUrl($public_profile_url)
    {
        $this->public_profile_url = $public_profile_url;
        
        return $this;
    }


    
}

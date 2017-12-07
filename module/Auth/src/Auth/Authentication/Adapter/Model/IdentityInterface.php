<?php

namespace Auth\Authentication\Adapter\Model;

interface IdentityInterface extends \JsonSerializable
{
    public function getEmail();

    public function getLastname();

    public function getFirstname();
    
    public function getNickname();

    public function getToken();

    public function getCreatedDate();

    public function getExpirationDate();
    
    public function getSuspensionDate();
    
    public function getSuspensionReason();

    public function exchangeArray(array $datas);
}

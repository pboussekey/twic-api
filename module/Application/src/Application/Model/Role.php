<?php

namespace Application\Model;

use Application\Model\Base\Role as BaseRole;

class Role extends BaseRole
{
    const ROLE_ADMIN_ID = 1;
    const ROLE_USER_ID = 2;
    const ROLE_EXTERNAL_ID = 3;

    const ROLE_ADMIN_STR = 'admin';
    const ROLE_USER_STR = 'user';
    const ROLE_EXTERNAL_STR = 'external';

    public static $role = [
      self::ROLE_USER_ID => self::ROLE_USER_STR,
      self::ROLE_ADMIN_ID => self::ROLE_ADMIN_STR,
      self::ROLE_EXTERNAL_ID => self::ROLE_EXTERNAL_STR,
    ];
}

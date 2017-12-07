CREATE 
     OR REPLACE 
	ALGORITHM = UNDEFINED 
VIEW `research` AS
    SELECT 
        `u`.`id` AS `id`,
        CONCAT(`u`.`firstname`, ' ', `u`.`lastname`) AS `firstname`,
        CONCAT(`u`.`lastname`, ' ', `u`.`firstname`) AS `lastname`,
        `nickname` AS `nickname`,
        `u`.`avatar` AS `avatar`,
        'user' AS `category`,
        `role`.`name` AS `role`,
        IF((`role`.`id` = 4), 10, 20) AS `facette`,
        `u`.`id` AS `user_id`,
        NULL AS `course_id`,
        NULL AS `school_id`
    FROM
        ((`user` `u`
        JOIN `user_role` `us`)
        JOIN `role`)
    WHERE
        ((`us`.`user_id` = `u`.`id`)
            AND (`us`.`role_id` = `role`.`id`)
            AND (`us`.`role_id` IN (4 , 5))
            AND ISNULL(`u`.`deleted_date`)) 
    UNION SELECT 
        `c`.`id` AS `id`,
        `c`.`title` AS `title`,
        `p`.`name` AS `name`,
        NULL AS `nickname`,
        `c`.`picture` AS `picture`,
        'course' AS `category`,
        '' AS `role`,
        '30' AS `facette`,
        NULL AS `user_id`,
        `c`.`id` AS `course_id`,
        NULL AS `school_id`
    FROM
        (`course` `c`
        JOIN `program` `p`)
    WHERE
        ((`c`.`program_id` = `p`.`id`)
            AND ISNULL(`p`.`deleted_date`)
            AND ISNULL(`c`.`deleted_date`)) 
    UNION SELECT 
        `s`.`id` AS `id`,
        `s`.`name` AS `name`,
        `s`.`short_name` AS `short_name`,
        NULL AS `nickname`,
        `s`.`logo` AS `logo`,
        'school' AS `category`,
        '' AS `role`,
        '40' AS `facette`,
        NULL AS `user_id`,
        NULL AS `course_id`,
        `s`.`id` AS `school_id`
    FROM
        `school` `s`
    WHERE
        ISNULL(`s`.`deleted_date`);

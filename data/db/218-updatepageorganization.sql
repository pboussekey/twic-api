ALTER TABLE `page` 
CHANGE COLUMN `type` `type` ENUM('group', 'event', 'course', 'organization') NULL DEFAULT NULL ;

ALTER TABLE `page_user`
CHANGE COLUMN `role` `role` ENUM('admin', 'user', 'faculty', 'ta') NOT NULL DEFAULT 'user' ;

/* 
- Copy tt les organisation dans la page 
*/
ALTER TABLE `page` 
ADD COLUMN `uid` VARCHAR(45) NULL AFTER `owner_id`,
ADD COLUMN `short_title` VARCHAR(45) NULL AFTER `uid`,
ADD COLUMN `website` VARCHAR(45) NULL AFTER `short_title`,
ADD COLUMN `phone` VARCHAR(45) NULL AFTER `website`,
ADD COLUMN `libelle` VARCHAR(45) NULL AFTER `phone`,
ADD COLUMN `custom` VARCHAR(45) NULL AFTER `libelle`,
ADD COLUMN `subtype` TEXT NULL AFTER `custom`;

INSERT INTO `page`
(`logo`,`description`,`background`,`deleted_date`,`title`,`uid`,`type`,`short_title`,`website`,`phone`,`address_id`,`libelle`,`custom`,`subtype`)
 SELECT `logo`,`describe`,`background`,`deleted_date`,`name`,`id`,"organization",`short_name`,`website`,`phone`,`address_id`,`libelle`,`custom`,`type` FROM `school`;

/*
- Copie du champ organization_id de la page dans page_id de la page
*/
SET SQL_SAFE_UPDATES=0;
UPDATE page p, page pp 
SET p.page_id = pp.id 
WHERE pp.uid = p.organization_id AND p.organization_id IS NOT NULL;

/*
- Supprimer organization_id de la page
*/
ALTER TABLE `page`
DROP FOREIGN KEY `fk_page_2`;
ALTER TABLE `page`
DROP COLUMN `organization_id`,
DROP INDEX `fk_page_2_idx` ;

/*
- Add user page
*/
INSERT IGNORE INTO `page_user`
(`page_id`,`user_id`,`role`,`state`)
SELECT page.id page_id, organization_user.user_id, IF(user_role.role_id = 1 || user_role.role_id = 2 || user_role.role_id = 3, 'admin', IF(user_role.role_id = 5, 'faculty', 'user')) as role, 'member' as state FROM organization_user
INNER JOIN page on organization_user.organization_id=page.uid
INNER JOIN user_role ON user_role.user_id=organization_user.user_id;

/*
- on creer la table de relation
*/
CREATE TABLE IF NOT EXISTS `page_relation` (
  `page_id` INT UNSIGNED NOT NULL,
  `parent_id` INT UNSIGNED NOT NULL,
  `type` VARCHAR(45) NULL,
  PRIMARY KEY (`page_id`, `parent_id`),
  INDEX `fk_page_relation_2_idx` (`parent_id` ASC),
  CONSTRAINT `fk_page_relation_1`
    FOREIGN KEY (`page_id`)
    REFERENCES `page` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_page_relation_2`
    FOREIGN KEY (`parent_id`)
    REFERENCES `page` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

/*
- on met a jour les relation existante
*/
INSERT IGNORE INTO `page_relation`
(`page_id`, `parent_id`, `type`)
SELECT id, page_id, "OWNER" FROM page WHERE page_id IS NOT NULL;

INSERT IGNORE INTO `page_relation`
(`page_id`, `parent_id`, `type`)
SELECT 
    p.id, pp.id, "MEMBER"
FROM 
    organization_relation
INNER JOIN page p on organization_relation.organization_id=p.uid 
INNER JOIN page pp on organization_relation.parent_id=pp.uid;

/*
- on supprime page_id de page
*/
ALTER TABLE `page` 
DROP FOREIGN KEY `fk_page_3`;
ALTER TABLE `page` 
DROP COLUMN `page_id`,
DROP INDEX `fk_page_3_idx` ;

/*
- update circle organization
*/
ALTER TABLE `circle_organization` 
DROP FOREIGN KEY `fk_circle_organization_2`;
ALTER TABLE `circle_organization` 
DROP INDEX `fk_circle_organization_2_idx` ;

SET SQL_SAFE_UPDATES=0;
UPDATE circle_organization, page
SET circle_organization.organization_id = page.id
WHERE circle_organization.organization_id=page.uid;

ALTER TABLE `circle_organization` 
ADD INDEX `fk_circle_organization_2_idx` (`organization_id` ASC);
ALTER TABLE `circle_organization` 
ADD CONSTRAINT `fk_circle_organization_2`
  FOREIGN KEY (`organization_id`)
  REFERENCES `page` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

/*
- update grading organization
*/
ALTER TABLE `grading` 
DROP FOREIGN KEY `fk_grading_1`;
ALTER TABLE `grading` 
DROP COLUMN `program_id`,
CHANGE COLUMN `school_id` `organization_id` INT(10) UNSIGNED NULL DEFAULT NULL ,
DROP INDEX `fk_grading_1_idx` ;

SET SQL_SAFE_UPDATES=0;
UPDATE grading, page
SET grading.organization_id = page.id
WHERE grading.organization_id=page.uid;

ALTER TABLE `grading`
ADD INDEX `fk_grading_1_idx` (`organization_id` ASC);
ALTER TABLE `grading`
ADD CONSTRAINT `fk_grading_1`
  FOREIGN KEY (`organization_id`)
  REFERENCES `page` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

/*
- Copie du champ organization_id dans page_id et t_organization_id dans t_page_id dans la page post
- Supprimer champ organization_id et t_organization_id de la page post 
*/
SET SQL_SAFE_UPDATES=0;
UPDATE post, page
SET post.page_id = page.id
WHERE post.organization_id=page.uid;

SET SQL_SAFE_UPDATES=0;
UPDATE post, page
SET post.t_page_id = page.id
WHERE post.t_organization_id=page.uid;

ALTER TABLE `post`
DROP FOREIGN KEY `fk_post_8`,
DROP FOREIGN KEY `fk_post_3`;
ALTER TABLE `post`
DROP COLUMN `t_organization_id`,
DROP COLUMN `organization_id`,
DROP INDEX `fk_post_8_idx` ,
DROP INDEX `fk_post_3_idx` ;


/*
- upadte user oraganization
*/
ALTER TABLE `user`
DROP FOREIGN KEY `fk_user_1`;
ALTER TABLE `user`
CHANGE COLUMN `school_id` `organization_id` INT(10) UNSIGNED NULL DEFAULT NULL ,
DROP INDEX `fk_user_1_idx` ;

SET SQL_SAFE_UPDATES=0;
UPDATE user, page
SET user.organization_id = page.id
WHERE user.organization_id=page.uid;

ALTER TABLE `user`
ADD INDEX `fk_user_1_idx` (`organization_id` ASC);
ALTER TABLE `user`
ADD CONSTRAINT `fk_user_1`
  FOREIGN KEY (`organization_id`)
  REFERENCES `page` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

DROP TABLE `program_user_relation`;
DROP TABLE `course_user_relation`;

ALTER TABLE `course`
DROP FOREIGN KEY `fk_course_2`;
ALTER TABLE `course`
DROP INDEX `fk_course_2_idx` ;

DROP TABLE `program`;

DROP TABLE `organization_relation`;
DROP TABLE `organization_user`;
DROP TABLE `school`;


UPDATE IGNORE `user_role`
SET `role_id` = 1 WHERE `role_id` = 2;
UPDATE IGNORE `user_role`
SET `role_id` = 2 WHERE `role_id` <> 2;

DELETE FROM `user_role`
WHERE `role_id` NOT IN (1, 2);

TRUNCATE `role_permission`;

UPDATE `role_relation` SET `parent_id`='2' WHERE `role_id`='0' and`parent_id`='4';
DELETE FROM `role_relation` WHERE `role_id`='3' and`parent_id`='2';
DELETE FROM `role_relation` WHERE `role_id`='4' and`parent_id`='5';
DELETE FROM `role_relation` WHERE `role_id`='5' and`parent_id`='3';


UPDATE `role` SET `name`='admin' WHERE `id`='1';
UPDATE `role` SET `name`='user' WHERE `id`='2';
DELETE FROM `role` WHERE `id`='7';
UPDATE `role` SET `name`='external' WHERE `id`='3';
DELETE FROM `role` WHERE `id`='4';
DELETE FROM `role` WHERE `id`='5';
DELETE FROM `role` WHERE `id`='6';


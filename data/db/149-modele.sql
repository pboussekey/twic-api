CREATE TABLE IF NOT EXISTS `tag` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NULL,
  `weight` INT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `post` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` VARCHAR(45) NULL,
  `content` TEXT NULL DEFAULT NULL,
  `user_id` INT(10) UNSIGNED NULL DEFAULT NULL,
  `link` TEXT NULL DEFAULT NULL,
  `video` VARCHAR(80) NULL DEFAULT NULL,
  `picture` VARCHAR(512) NULL DEFAULT NULL,
  `name_picture` VARCHAR(255) NULL DEFAULT NULL,
  `link_title` VARCHAR(512) NULL DEFAULT NULL,
  `link_desc` TEXT NULL DEFAULT NULL,
  `created_date` DATETIME NULL DEFAULT NULL,
  `deleted_date` DATETIME NULL DEFAULT NULL,
  `updated_date` DATETIME NULL,
  `parent_id` INT NULL,
  `t_page_id` INT UNSIGNED NULL,
  `t_organization_id` INT NULL,
  `t_user_id` INT NULL,
  `t_course_id` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_feed_1_idx` (`user_id` ASC),
  CONSTRAINT `fk_feed_10`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `page` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NULL,
  `logo` VARCHAR(80) NULL,
  `background` VARCHAR(80) NULL,
  `description` TEXT NULL,
  `confidentiality` INT NULL,
  `admission` ENUM('free', 'open', 'invitation') NULL,
  `start_date` DATETIME NULL,
  `end_date` DATETIME NULL,
  `location` VARCHAR(255) NULL,
  `type` ENUM('group', 'event') NULL,
  `user_id` INT UNSIGNED NULL,
  `organization_id` INT UNSIGNED NULL,
  `page_id` INT UNSIGNED NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_page_1_idx` (`user_id` ASC),
  INDEX `fk_page_2_idx` (`organization_id` ASC),
  INDEX `fk_page_3_idx` (`page_id` ASC),
  CONSTRAINT `fk_page_1`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_page_2`
    FOREIGN KEY (`organization_id`)
    REFERENCES `school` (`id`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_page_3`
    FOREIGN KEY (`page_id`)
    REFERENCES `page` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `post_like` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `is_like` TINYINT(1) NOT NULL DEFAULT '1',
  `user_id` INT(10) UNSIGNED NOT NULL,
  `post_id` INT(10) UNSIGNED NOT NULL,
  `created_date` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_post_like_1_idx` (`user_id` ASC),
  INDEX `fk_post_like_2_idx` (`post_id` ASC),
  CONSTRAINT `fk_post_like_1`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_post_like_2`
    FOREIGN KEY (`post_id`)
    REFERENCES `post` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `post_doc` (
  `post_id` INT UNSIGNED NOT NULL,
  `library_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NULL,
  PRIMARY KEY (`post_id`, `library_id`),
  INDEX `fk_page_doc_2_idx` (`library_id` ASC),
  INDEX `fk_page_doc_3_idx` (`user_id` ASC),
  CONSTRAINT `fk_page_doc_10`
    FOREIGN KEY (`post_id`)
    REFERENCES `post` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_page_doc_20`
    FOREIGN KEY (`library_id`)
    REFERENCES `library` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_page_doc_30`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `page_tag` (
  `page_id` INT UNSIGNED NOT NULL,
  `tag_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`page_id`, `tag_id`),
  INDEX `fk_sgroup_tag_1_idx` (`tag_id` ASC),
  CONSTRAINT `fk_sgroup_tag_1`
    FOREIGN KEY (`tag_id`)
    REFERENCES `tag` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_sgroup_tag_2`
    FOREIGN KEY (`page_id`)
    REFERENCES `page` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `page_user` (
  `page_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `role` ENUM('admin', 'user') NULL,
  `state` ENUM('pending', 'member', 'invited', 'rejected') NULL,
  PRIMARY KEY (`page_id`, `user_id`),
  INDEX `fk_sgroup_user_2_idx` (`user_id` ASC),
  CONSTRAINT `fk_sgroup_user_1`
    FOREIGN KEY (`page_id`)
    REFERENCES `page` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_sgroup_user_2`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `page_doc` (
  `page_id` INT UNSIGNED NOT NULL,
  `library_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`page_id`, `library_id`),
  INDEX `fk_page_doc_2_idx` (`library_id` ASC),
  CONSTRAINT `fk_page_doc_1`
    FOREIGN KEY (`page_id`)
    REFERENCES `page` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_page_doc_2`
    FOREIGN KEY (`library_id`)
    REFERENCES `library` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


INSERT IGNORE INTO `permission` (`libelle`) VALUES ('page.add');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'page.add'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('page.update');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'page.update'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('page.delete');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 
(SELECT `id` FROM `permission` WHERE `libelle`= 'page.delete'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('page.get');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4,
(SELECT `id` FROM `permission` WHERE `libelle`= 'page.get'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('page.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (4,
(SELECT `id` FROM `permission` WHERE `libelle`= 'page.getList'));

DROP TABLE IF EXISTS `post_like`;
DROP TABLE IF EXISTS `post_doc`; 
DROP TABLE `post`;
CREATE TABLE IF NOT EXISTS `post` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `content` TEXT NULL DEFAULT NULL,
  `user_id` INT(10) UNSIGNED NULL DEFAULT NULL,
  `organization_id` INT UNSIGNED NULL,
  `page_id` INT UNSIGNED NULL,
  `link` TEXT NULL DEFAULT NULL,
  `picture` VARCHAR(512) NULL DEFAULT NULL,
  `name_picture` VARCHAR(255) NULL DEFAULT NULL,
  `link_title` VARCHAR(512) NULL DEFAULT NULL,
  `link_desc` TEXT NULL DEFAULT NULL,
  `created_date` DATETIME NULL DEFAULT NULL,
  `deleted_date` DATETIME NULL DEFAULT NULL,
  `updated_date` DATETIME NULL,
  `parent_id` INT UNSIGNED NULL,
  `origin_id` INT UNSIGNED NULL,
  `t_page_id` INT UNSIGNED NULL,
  `t_organization_id` INT UNSIGNED NULL,
  `t_user_id` INT UNSIGNED NULL,
  `t_course_id` INT UNSIGNED NULL,
  `lat` DOUBLE NULL,
  `lng` DOUBLE NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_feed_1_idx` (`user_id` ASC),
  INDEX `fk_post_2_idx` (`origin_id` ASC),
  INDEX `fk_post_3_idx` (`organization_id` ASC),
  INDEX `fk_post_4_idx` (`page_id` ASC),
  INDEX `fk_post_5_idx` (`parent_id` ASC),
  INDEX `fk_post_7_idx` (`t_page_id` ASC),
  INDEX `fk_post_8_idx` (`t_organization_id` ASC),
  INDEX `fk_post_9_idx` (`t_user_id` ASC),
  INDEX `fk_post_10_idx` (`t_course_id` ASC),
  CONSTRAINT `fk_post_1`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_post_2`
    FOREIGN KEY (`origin_id`)
    REFERENCES `post` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_post_3`
    FOREIGN KEY (`organization_id`)
    REFERENCES `school` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_post_4`
    FOREIGN KEY (`page_id`)
    REFERENCES `page` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_post_5`
    FOREIGN KEY (`parent_id`)
    REFERENCES `post` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_post_6`
    FOREIGN KEY (`origin_id`)
    REFERENCES `post` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_post_7`
    FOREIGN KEY (`t_page_id`)
    REFERENCES `page` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_post_8`
    FOREIGN KEY (`t_organization_id`)
    REFERENCES `school` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_post_9`
    FOREIGN KEY (`t_user_id`)
    REFERENCES `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_post_10`
    FOREIGN KEY (`t_course_id`)
    REFERENCES `course` (`id`)
    ON DELETE NO ACTION
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


CREATE TABLE IF NOT EXISTS `subscription` (
  `libelle` VARCHAR(80) NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `created_date` DATETIME NOT NULL,
  PRIMARY KEY (`libelle`, `user_id`),
  INDEX `fk_subscription_user_1_idx` (`user_id` ASC),
  INDEX `idx_subscription_user_1` (`libelle` ASC),
  CONSTRAINT `fk_subscription_user_1`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `post_subscription` (
  `libelle` VARCHAR(80) NOT NULL,
  `post_id` INT UNSIGNED NOT NULL,
  `last_date` DATETIME NULL,
  PRIMARY KEY (`libelle`, `post_id`),
  INDEX `fk_post_subscription_1_idx` (`post_id` ASC),
  INDEX `idx_post_subscription` (`libelle` ASC),
  CONSTRAINT `fk_post_subscription_1`
    FOREIGN KEY (`post_id`)
    REFERENCES `post` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

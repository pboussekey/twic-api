DROP TABLE IF EXISTS `post_user` ;

CREATE TABLE IF NOT EXISTS `post_user` (
  `post_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `hidden` TINYINT NULL DEFAULT 0,
  PRIMARY KEY (`post_id`, `user_id`),
  INDEX `fk_post_user_2_idx` (`user_id` ASC),
  CONSTRAINT `fk_post_user_1`
    FOREIGN KEY (`post_id`)
    REFERENCES `post` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_post_user_2`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;
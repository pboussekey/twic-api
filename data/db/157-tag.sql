CREATE TABLE IF NOT EXISTS `hashtag` (
  `name` VARCHAR(80) NOT NULL,
  `post_id` INT UNSIGNED NOT NULL,
  `type` ENUM('#', '@') NOT NULL,
  `created_date` DATETIME NULL,
  PRIMARY KEY (`name`, `post_id`),
  INDEX `fk_hashtag_1_idx` (`post_id` ASC),
  CONSTRAINT `fk_hashtag_1`
    FOREIGN KEY (`post_id`)
    REFERENCES `post` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

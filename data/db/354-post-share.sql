ALTER TABLE `post`
ADD COLUMN  `shared_id` INT(10) UNSIGNED NULL DEFAULT NULL;


ALTER TABLE `post`
ADD CONSTRAINT`fk_post_109`
    FOREIGN KEY (`shared_id`)
    REFERENCES `post` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION;
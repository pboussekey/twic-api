CREATE TABLE IF NOT EXISTS `user_tag` (
  `user_id` INT(10) UNSIGNED NOT NULL,
  `tag_id` INT(10) UNSIGNED NOT NULL,
  `category` ENUM('expertise', 'interest', 'language') NOT NULL DEFAULT 'interest',
  INDEX `fk_sgroup_tag_1_idx` (`tag_id` ASC),
  INDEX `fk_sgroup_tag_2_idx` (`user_id` ASC),
  CONSTRAINT `fk_user_tag_1`
    FOREIGN KEY (`tag_id`)
    REFERENCES `tag` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_tag_2`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

ALTER TABLE `user` ADD COLUMN `description` TEXT NULL;
SET SQL_SAFE_UPDATES=0;
UPDATE user SET description = (SELECT description FROM resume WHERE type = 0 AND user_id = user.id LIMIT 1);

INSERT INTO tag(name, weight)
SELECT title, COUNT(*) FROM resume WHERE type = 8 GROUP BY title;

INSERT INTO user_tag(user_id, tag_id, category)
SELECT resume.user_id, tag.id, 'language' FROM resume
JOIN tag ON resume.title = tag.name
WHERE type = 8;

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.addTag');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.addTag'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.getDescription');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.getDescription'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('user.removeTag');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'user.removeTag'));

INSERT IGNORE INTO `permission` (`libelle`) VALUES ('tag.getList');
INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES (2,
(SELECT `id` FROM `permission` WHERE `libelle`= 'tag.getList'));

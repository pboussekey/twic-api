ALTER TABLE `library` 
ADD COLUMN `global` TINYINT NULL DEFAULT 0 AFTER `box_id`;

INSERT INTO `library` (`global`,`name`) VALUES (true, 'public');

UPDATE `library` SET `global` = false WHERE `global` IS NULL;

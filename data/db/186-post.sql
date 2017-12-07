ALTER TABLE `post` 
DROP COLUMN `data`;

ALTER TABLE `post_subscription` 
ADD COLUMN `data` TEXT NULL AFTER `user_id`;

ALTER TABLE `post_subscription` 
CHANGE COLUMN `action` `action` VARCHAR(255) NOT NULL ;

ALTER TABLE `post` 
DROP COLUMN `event`;



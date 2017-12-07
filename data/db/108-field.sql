ALTER TABLE `whiteboard` 
ADD COLUMN `width` INT NULL AFTER `owner_id`,
ADD COLUMN `height` INT NULL AFTER `width`;


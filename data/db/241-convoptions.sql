ALTER TABLE `conversation` 
ADD COLUMN `options` TEXT NULL AFTER `created_date`;

ALTER TABLE `conversation`
DROP FOREIGN KEY `fk_conversation_1`;
ALTER TABLE `conversation`
DROP COLUMN `conversation_opt_id`,
DROP INDEX `fk_conversation_1_idx` ;

DROP TABLE `conversation_opt`;

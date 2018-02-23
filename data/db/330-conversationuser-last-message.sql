ALTER TABLE `conversation_user` 
ADD COLUMN `last_message` INT(10) UNSIGNED NULL AFTER `conversation_id`;

ALTER TABLE `conversation_user`
ADD CONSTRAINT`fk_conversation_user_3`
    FOREIGN KEY (`last_message`)
    REFERENCES `message` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION;


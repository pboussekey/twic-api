ALTER TABLE `report` 
ADD COLUMN  `conversation_id` INT(10) UNSIGNED NULL DEFAULT NULL;


ALTER TABLE `report`
ADD CONSTRAINT`fk_report_5`
    FOREIGN KEY (`conversation_id`)
    REFERENCES `conversation` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION;

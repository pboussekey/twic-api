ALTER TABLE `bank_question` 
CHANGE COLUMN `older` `older` INT UNSIGNED NULL DEFAULT NULL ,
ADD INDEX `fk_bank_question_10_idx` (`older` ASC);

ALTER TABLE `bank_question` 
ADD CONSTRAINT `fk_bank_question_10`
  FOREIGN KEY (`older`)
  REFERENCES `bank_question` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

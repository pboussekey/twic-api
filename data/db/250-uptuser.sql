UPDATE `page`
SET
`confidentiality` = 0
WHERE `confidentiality` IS NULL;
UPDATE `page`
SET
`admission` = 'open'
WHERE `admission` IS NULL;

ALTER TABLE `page` 
CHANGE COLUMN `confidentiality` `confidentiality` INT(11) NULL DEFAULT 0 ,
CHANGE COLUMN `admission` `admission` ENUM('free', 'open', 'invitation') NULL DEFAULT 'open' ;

SET SQL_SAFE_UPDATES = 0;
UPDATE `resume`
SET `title`=(@temp:=`title`), `title` = `subtitle`, `subtitle` = @temp
WHERE `type` IN (1,2,4);

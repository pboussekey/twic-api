ALTER TABLE `submission_comments` 
DROP FOREIGN KEY `fk_submission_comment_10`;
ALTER TABLE `submission_comments` 
ADD INDEX `fk_submission_comment_10_idx` (`submission_id` ASC),
DROP INDEX `fk_submission_comment_10_idx` ;
ALTER TABLE `submission_comments` 
ADD CONSTRAINT `fk_submission_comment_10`
  FOREIGN KEY (`submission_id`)
  REFERENCES `submission` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;
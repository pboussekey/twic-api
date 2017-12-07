ALTER TABLE `submission_comments` 
DROP FOREIGN KEY `fk_submission_comment_10`;
ALTER TABLE `submission_comments` 
ADD CONSTRAINT `fk_submission_comment_10`
  FOREIGN KEY (`submission_id`)
  REFERENCES `submission` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;


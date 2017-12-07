ALTER TABLE `submission` 
DROP FOREIGN KEY `fk_submission_2`;
ALTER TABLE `submission` 
ADD CONSTRAINT `fk_submission_2`
  FOREIGN KEY (`post_id`)
  REFERENCES `post` (`id`)
  ON DELETE SET NULL
  ON UPDATE NO ACTION;

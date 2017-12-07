ALTER TABLE `item` 
DROP FOREIGN KEY `fk_item_2`,
DROP FOREIGN KEY `fk_item_3`,
DROP FOREIGN KEY `fk_item_4`,
DROP FOREIGN KEY `fk_item_1`;

ALTER TABLE `item` 
DROP COLUMN `coefficient`,
DROP COLUMN `has_all_student`,
DROP COLUMN `is_grouped`,
DROP COLUMN `is_graded`,
DROP COLUMN `cut_off`,
DROP COLUMN `has_submission`,
DROP COLUMN `order_id`,
DROP COLUMN `set_id`,
DROP COLUMN `duration`,
DROP COLUMN `grading_policy_id`,
DROP COLUMN `course_id`,
DROP COLUMN `type`;

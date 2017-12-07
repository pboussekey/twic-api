ALTER TABLE `conversation_opt` 
ADD COLUMN `has_eqcq` TINYINT NOT NULL DEFAULT 0 AFTER `allow_intructor`;

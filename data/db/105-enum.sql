ALTER TABLE `item` 
CHANGE COLUMN `type` `type` ENUM('LC', 'WG', 'CP', 'IA', 'DOC', 'TXT', 'POLL', 'MOD', 'DISC', 'CHAT', 'EQCQ', 'HANGOUT') NOT NULL ;

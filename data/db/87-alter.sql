SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE  table_name = 'conversation_opt'
        AND table_schema = DATABASE()
        AND column_name = 'id'
    ) > 0,
    "SELECT 1",
    "ALTER TABLE `conversation_opt`
    ADD COLUMN `id` INT UNSIGNED NOT NULL AUTO_INCREMENT FIRST,
    ADD PRIMARY KEY (`id`);"
));

PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

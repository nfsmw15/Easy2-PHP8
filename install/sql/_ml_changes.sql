SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE TABLE `[prefix]_ml_changes` (
  `id` int(16) UNSIGNED NOT NULL,
  `code` varchar(32) DEFAULT NULL COMMENT 'Wiederherstellungscode',
  `timestamp` bigint(16) DEFAULT '0' COMMENT 'Wann Änderung vorgenommen wurde',
  `coloum` varchar(32) DEFAULT NULL COMMENT 'Welche Spalte geändert wurde',
  `value` varchar(1024) DEFAULT NULL COMMENT 'Alter Wert',
  `author` int(16) DEFAULT '0' COMMENT 'Wer Änderung vorgenommen hat',
  `changed` bigint(16) DEFAULT '0' COMMENT 'Timetsamp andem es Rückgeändert wurde',
  `user` int(16) DEFAULT '0' COMMENT 'Bezug zum User'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE `[prefix]_ml_changes` ADD PRIMARY KEY (`id`);
ALTER TABLE `[prefix]_ml_changes` MODIFY `id` int(16) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

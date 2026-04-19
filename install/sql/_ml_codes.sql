SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE TABLE `[prefix]_ml_codes` (
  `id` int(10) UNSIGNED NOT NULL,
  `uik` varchar(32) DEFAULT NULL COMMENT 'Benutzer',
  `code` varchar(32) DEFAULT NULL COMMENT 'Code',
  `action` varchar(128) DEFAULT NULL COMMENT 'Aktion',
  `expiry_date` bigint(16) DEFAULT '0' COMMENT 'Timestamp des verfalls | 0 = nie'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE `[prefix]_ml_codes` ADD PRIMARY KEY (`id`);
ALTER TABLE `[prefix]_ml_codes` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

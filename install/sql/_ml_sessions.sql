SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE TABLE `[prefix]_ml_sessions` (
  `id` int(16) UNSIGNED NOT NULL,
  `uik` varchar(32) DEFAULT NULL COMMENT 'User Identification Key',
  `sic` varchar(32) DEFAULT NULL COMMENT 'Session Identification Code',
  `ult` bigint(16) DEFAULT '0' COMMENT 'User Login Time',
  `ulc` varchar(32) DEFAULT NULL COMMENT 'User Login Code',
  `last_action` bigint(16) NOT NULL DEFAULT '0',
  `logout` bigint(16) NOT NULL DEFAULT '0',
  `locked` int(1) DEFAULT '0' COMMENT 'Gesperrt',
  `closed` int(1) DEFAULT '0' COMMENT 'Geschlossen',
  `locked_dir` varchar(512) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE `[prefix]_ml_sessions` ADD PRIMARY KEY (`id`);
ALTER TABLE `[prefix]_ml_sessions` MODIFY `id` int(16) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
COMMIT;

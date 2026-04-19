SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE TABLE `[prefix]_ml_user` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(30) DEFAULT NULL,
  `password` varchar(128) DEFAULT NULL,
  `email` varchar(60) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `rank` int(10) NOT NULL DEFAULT '1',
  `regdate` varchar(40) DEFAULT NULL,
  `first_name` varchar(64) DEFAULT NULL,
  `last_name` varchar(64) DEFAULT NULL,
  `uik` varchar(32) DEFAULT NULL,
  `avatar` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `[prefix]_ml_user` (`id`, `username`, `password`, `email`, `active`, `rank`, `regdate`, `first_name`, `last_name`, `uik`, `avatar`) VALUES 
(1, 'System', '', '', 0, 1, '', 'System', '', '3A2xdfRKw5k6IqptThiZSFXbT5J0oELO', ''), 
(2, 'Gast', '', '', 0, 1813201542, '', 'Gast', '', '3A2xdfRKw9l6IqptThiZSFXbT5J0oELO', '');
ALTER TABLE `[prefix]_ml_user` ADD PRIMARY KEY (`id`);
ALTER TABLE `[prefix]_ml_user` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

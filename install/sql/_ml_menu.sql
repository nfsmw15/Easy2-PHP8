SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE TABLE `[prefix]_ml_menu` (
  `id` bigint(16) UNSIGNED NOT NULL,
  `sid` bigint(16) DEFAULT '0' COMMENT 'Site ID',
  `title` varchar(64) DEFAULT NULL COMMENT 'Titel',
  `icon` varchar(32) DEFAULT NULL COMMENT 'Icon',
  `pos` int(11) DEFAULT '0' COMMENT 'Position',
  `url` varchar(1024) DEFAULT NULL COMMENT 'URL für externe Links',
  `under` int(11) DEFAULT '0' COMMENT 'Untergeordnet (ID vom Dropdownlink)',
  `menu` int(11) NOT NULL DEFAULT '1' COMMENT 'Welches Menu untergeordnet',
  `link_type` int(1) NOT NULL DEFAULT '0' COMMENT '0 = neutral / 1 = eingeloggt nicht sichtbar',
  `target` varchar(8) NOT NULL DEFAULT '_self'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `[prefix]_ml_menu` (`id`, `sid`, `title`, `icon`, `pos`, `url`, `under`, `menu`, `link_type`, `target`) VALUES
(1, 2, 'Dashboard', 'fa-dashboard', 0, '', 0, 1, 0, '_self'),
(3, 17, 'Login', '', 0, '', 15, 1, 1, '_self'),
(4, 0, 'Mein Profil', 'fa-user', 3, '', 0, 1, 0, '_self'),
(5, 4, 'Mein Profil', 'fa-user', 0, '', 4, 1, 0, '_self'),
(6, 0, 'Sperren', 'fa-lock', 1, './?c=lock&csrf=[csrf]', 4, 1, 0, '_self'),
(7, 0, 'Abmelden', 'fa-power-off', 2, './?c=logout&csrf=[csrf]', 4, 1, 0, '_self'),
(8, 0, 'Einstellungen', 'fa-cogs', 4, '', 0, 1, 0, '_self'),
(9, 7, 'Benutzer verwalten', 'fa-users', 0, '', 8, 1, 0, '_self'),
(10, 6, 'Einstellungen', 'fa-cog', 1, '', 8, 1, 0, '_self'),
(11, 1, 'Ränge verwalten', 'fa-university', 3, '', 8, 1, 0, '_self'),
(12, 11, 'Regeln verwalten', 'fa-key', 4, '', 8, 1, 0, '_self'),
(13, 12, 'Seiten verwalten', 'fa-files-o', 5, '', 8, 1, 0, '_self'),
(14, 18, 'Menü verwalten', 'fa-align-left', 2, '', 8, 1, 0, '_self'),
(15, 0, 'Anmelden', '', 5, '', 0, 1, 1, '_self'),
(16, 14, 'Registrieren', '', 1, '', 15, 1, 1, '_self'),
(17, 8, 'Kontakt', 'fa-envelope', 1, '', 0, 1, 0, '_self'),
(18, 0, 'Weiteres', 'fa-list-ul', 2, '', 0, 1, 0, '_self'),
(20, 15, '404', 'fa-flash', 0, '', 18, 1, 0, '_self'),
(22, 3, 'Impressum', 'fa-legal', 1, '', 18, 1, 0, '_self'),
(23, 21, 'Datenschutz', 'fa-shield', 2, '', 18, 1, 0, '_self');

ALTER TABLE `[prefix]_ml_menu`
  ADD PRIMARY KEY (`id`);
  
ALTER TABLE `[prefix]_ml_menu`
  MODIFY `id` bigint(16) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
COMMIT;
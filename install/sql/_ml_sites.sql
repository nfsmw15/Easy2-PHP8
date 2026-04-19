SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE TABLE `[prefix]_ml_sites` (
  `id` bigint(16) NOT NULL,
  `filename` varchar(64) DEFAULT NULL COMMENT 'Dateiname',
  `dir` varchar(128) DEFAULT NULL COMMENT 'Verzeichnis',
  `title` varchar(32) DEFAULT NULL COMMENT 'Seitentitle',
  `start_site` bigint(16) DEFAULT '0' COMMENT 'Startseite',
  `start_site_login` bigint(16) DEFAULT '0' COMMENT 'Startseite nach login',
  `errorsite` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Ist dies die Errorseite? 1 = yes',
  `type` varchar(16) NOT NULL DEFAULT 'php',
  `logout_site` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `[prefix]_ml_sites` (`id`, `filename`, `dir`, `title`, `start_site`, `start_site_login`, `errorsite`, `type`, `logout_site`) VALUES
(1, 'ranks', 'adm/', 'Ränge verwalten', 0, 0, 0, 'php', 0),
(2, 'home', '', 'Startseite', 0, 1, 0, 'php', 0),
(3, 'impressum', '', 'Impressum', 0, 0, 0, 'php', 0),
(4, 'profil', 'login/', 'Profil', 0, 0, 0, 'php', 0),
(6, 'settings', 'adm/', 'Einstellungen', 0, 0, 0, 'php', 0),
(7, 'userlist', 'adm/', 'Benutzer verwalten', 0, 0, 0, 'php', 0),
(8, 'contact', 'bootstrap/', 'Kontakt', 0, 0, 0, 'php', 0),
(10, 'locked', 'login/', 'Gesperrt', 0, 0, 0, 'php', 0),
(11, 'rules', 'adm/', 'Regeln verwalten', 0, 0, 0, 'php', 0),
(12, 'sites', 'adm/', 'Seiten verwalten', 0, 0, 0, 'php', 0),
(13, 'pwv', 'login/', 'Passwort vergessen', 0, 0, 0, 'php', 0),
(14, 'regist', 'login/', 'Registrieren', 0, 0, 0, 'php', 0),
(15, '404', '', '404', 0, 0, 1, 'php', 0),
(17, 'login', 'login/', 'Anmeldung', 1, 0, 0, 'php', 1),
(18, 'menu', 'adm/', 'Menüverwaltung', 0, 0, 0, 'php', 0),
(19, 'additional_fields', 'adm/', 'Zusatzfelder', 0, 0, 0, 'php', 0),
(20, 'pw_reset', 'login/', 'Passwort zurücksetzen', 0, 0, 0, 'php', 0),
(21, 'privacy_policy', '', 'Datenschutz', 0, 0, 0, 'php', 0);

ALTER TABLE `[prefix]_ml_sites`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `[prefix]_ml_sites`
  MODIFY `id` bigint(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
COMMIT;
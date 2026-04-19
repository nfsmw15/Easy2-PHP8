SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE TABLE `[prefix]_ml_fields` (
  `id` bigint(16) UNSIGNED NOT NULL,
  `name` varchar(64) DEFAULT NULL COMMENT 'Name in HTML',
  `title` varchar(64) DEFAULT NULL COMMENT 'Für Benutzer sichtbar',
  `type` varchar(64) DEFAULT NULL COMMENT 'Feldtyp',
  `placeholder` varchar(128) DEFAULT NULL,
  `maxlength` int(11) NOT NULL DEFAULT '0',
  `required` int(1) NOT NULL DEFAULT '0' COMMENT 'Pflichtfeld?',
  `value` varchar(1024) DEFAULT NULL COMMENT 'Standard Wert',
  `description` text COMMENT 'Beschreibung',
  `options` text COMMENT 'Optionen (Bei select, radio, checkbox)',
  `pos` int(11) DEFAULT '0' COMMENT 'position',
  `regist` int(1) NOT NULL DEFAULT '0' COMMENT 'bei der Registrierung anzeigen?',
  `regex` text,
  `regex_options` varchar(8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Zusätzliche Felder für das Profil';

ALTER TABLE `[prefix]_ml_fields`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `[prefix]_ml_fields`
  MODIFY `id` bigint(16) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
COMMIT;
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE TABLE `[prefix]_ml_rules` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(40) DEFAULT NULL,
  `tag` varchar(40) DEFAULT NULL,
  `description` varchar(256) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `[prefix]_ml_rules` (`id`, `name`, `tag`, `description`) VALUES
(1, 'Benutzer bearbeiten', 'user_edit', 'Darf einen Benutzer bearbeiten'),
(2, 'Benutzer löschen', 'user_delete', 'Darf einen Benutzer löschen'),
(3, 'Benutzer aktivieren', 'user_enable', 'Darf einen Benutzer aktivieren'),
(4, 'Benutzer deaktivieren', 'user_disable', 'Darf einen Benutzer deaktivieren'),
(5, 'Haupteinstellungen', 'mainsave', 'Darf die Haupteinstellungen ändern'),
(8, 'Benutzer Rang', 'user_rank', 'Darf den Rang der Benutzer ändern'),
(160, 'Menü Positionen resetten', 'menu_reset_pos', 'Darf die Positionen der Menüpunkte resetten'),
(29, 'Rang hinzufügen', 'rank_new', 'Darf einen Rang hinzufügen, mit maximal den gleichen Berechtigungen'),
(30, 'Rang verschieben', 'rank_move', 'Darf einen Rang verschieben'),
(31, 'Rang entfernen', 'rank_delete', 'Darf einen Rang entfernen'),
(32, 'Rang bearbeiten', 'rank_edit', 'Darf einen Rang bearbeiten'),
(33, 'Standard Rang setzten', 'rank_default', 'Darf den Standard Rang ändern'),
(37, 'Benutzer Passwort zurücksetzen', 'user_pwreset', 'Darf Benutzerpasswörter zurücksetzen'),
(154, 'Seite hinzufügen', 'site_add', 'Darf eine Seite hinzufügen'),
(155, 'Seite bearbeiten', 'site_edit', 'Darf eine Seite bearbeiten'),
(156, 'Seite löschen', 'site_remove', 'Darf eine Seite löschen'),
(157, 'Menüpunkt hinzufügen', 'menu_add', 'Darf Menüpunkte hinzufügen'),
(158, 'Menüpunkt bearbeiten', 'menu_edit', 'Darf Menüpunkte bearbeiten'),
(159, 'Menüpunkt löschen', 'menu_remove', 'Darf Menüpunkte löschen'),
(83, 'Benutzer hinzufügen', 'user_add', 'Darf einen Benutzer hinzufügen'),
(138, 'Benutzer anzeigen', 'user_show', 'Darf Benutzer Informationen einsehen'),
(139, 'Benutzer Profilbild entfernen', 'user_rm_avatar', 'Darf die Benutzer Profilbilder entfernen'),
(164, 'Felder löschen', 'fields_remove', 'Darf zusätzliche Felder zum Profil löschen'),
(163, 'Felder bearbeiten', 'fields_edit', 'Darf zusätzliche Felder zum Profil berabiten'),
(162, 'Felder hinzufügen', 'fields_add', 'Darf zusätzliche Felder zum Profil hinzufügen'),
(161, 'Menü Lücken schließen', 'menu_fill_gaps', 'Draf im Menü die Lücken in den Positionsnummern schließen');

ALTER TABLE `[prefix]_ml_rules`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `[prefix]_ml_rules`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165;
COMMIT;
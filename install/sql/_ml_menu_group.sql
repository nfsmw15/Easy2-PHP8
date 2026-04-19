SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE TABLE `[prefix]_ml_menu_group` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `[prefix]_ml_menu_group` (`id`, `name`) VALUES
(1, 'Hauptmenü');

ALTER TABLE `[prefix]_ml_menu_group`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `[prefix]_ml_menu_group`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;
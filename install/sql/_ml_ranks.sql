SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE TABLE `[prefix]_ml_ranks` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `sites` text,
  `default` int(1) NOT NULL DEFAULT '0',
  `rules` text,
  `pos` int(10) DEFAULT NULL,
  `guest` int(1) NOT NULL DEFAULT '0' COMMENT 'Gast? Gast ist ein nicht eingeloggter Benutzer',
  `color` varchar(6) DEFAULT NULL COMMENT 'Hex Wert #xxxxxx',
  `special` varchar(64) DEFAULT NULL COMMENT 'bold, italic, underline'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `[prefix]_ml_ranks` (`id`, `title`, `sites`, `default`, `rules`, `pos`, `guest`, `color`, `special`) VALUES
(1813201540, 'Mitglied', '9,21,22,23,8,26,24,2,3,10,15,16,17,27,4,5,25,m10,m9', 1, '', 2, 0, '3d8a00', ''),
(1813201549, 'Administrator', '9,34,21,22,23,8,26,24,2,3,10,30,15,16,17,18,19,27,4,1,11,5,6,25,12,7,m7,m11,m10,m9', 0, 'fields_add,fields_edit,fields_remove,mainsave,menu_add,menu_edit,menu_fill_gaps,menu_remove,menu_reset_pos,rank_default,rank_delete,rank_edit,rank_move,rank_new,site_add,site_edit,site_remove,user_add,user_delete,user_disable,user_edit,user_enable,user_pwreset,user_rank,user_rm_avatar,user_show', 1, 0, '', 'bold'),
(1813201541, 'Webmaster', 'all', 0, 'all', 0, 0, 'aa0000', 'bold'),
(1813201542, 'Gast', '9,21,22,23,8,26,24,2,3,29,15,16,17,19,27,13,35,14,5,25,m19', 0, '', 3, 1, '', '');

ALTER TABLE `[prefix]_ml_ranks`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `[prefix]_ml_ranks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1813201559;
COMMIT;
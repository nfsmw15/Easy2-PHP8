SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE TABLE `[prefix]_ml_main` (
  `id` int(10) UNSIGNED NOT NULL,
  `tag` varchar(64) DEFAULT NULL,
  `value` varchar(1024) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `[prefix]_ml_main` (`id`, `tag`, `value`) VALUES
(2, 'site_title', 'EASY 2.0 Loginsystem'),
(3, 'short_site_title', 'EASY 2.0'),
(4, 'administrator_mail', ''),
(5, 'mail_sender', ''),
(6, 'mail_receiver', ''),
(7, 'useradministration_share', '1'),
(8, 'password_length', '6'),
(9, 'restore', '1'),
(10, 'cookielifetime', '7776000'),
(11, 'regist_active', '1'),
(12, 'pwv_active', '1'),
(13, 'user_activation_mode', '1'),
(14, 'default_avatar_type', 'jpg'),
(15, 'dsgvo_email', ''),
(16, 'captcha_type', 'default'),
(17, 'impressum_info', 'Musterfirma\r\nMax Mustermann\r\nMusterstrasse 123\r\n12345 Musterort\r\n\r\nTel: 0123 / 456789'),
(18, 'privacy_policy_info', 'Musterfirma\r\nMax Mustermann\r\nMusterstrasse 123\r\n12345 Musterort\r\n\r\nTel: 0123 / 456789');

ALTER TABLE `[prefix]_ml_main`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `[prefix]_ml_main`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
COMMIT;

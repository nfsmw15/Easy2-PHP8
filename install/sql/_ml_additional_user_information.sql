SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE TABLE `[prefix]_ml_additional_user_information` (
  `id` bigint(16) UNSIGNED NOT NULL,
  `field_id` bigint(16) DEFAULT '0',
  `value` text,
  `user_id` bigint(16) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `[prefix]_ml_additional_user_information`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `[prefix]_ml_additional_user_information`
  MODIFY `id` bigint(16) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
COMMIT;
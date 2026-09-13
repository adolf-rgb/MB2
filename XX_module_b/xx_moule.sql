SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


CREATE TABLE `companies` (
  `id` int(11) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `company_address` varchar(500) NOT NULL,
  `company_telephone` varchar(100) NOT NULL,
  `company_email` varchar(255) NOT NULL,
  `owner_name` varchar(255) NOT NULL,
  `owner_mobile` varchar(100) NOT NULL,
  `owner_email` varchar(255) NOT NULL,
  `contact_name` varchar(255) NOT NULL,
  `contact_mobile` varchar(100) NOT NULL,
  `contact_email` varchar(255) NOT NULL,
  `is_deactivated` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `companies` (`id`, `company_name`, `company_address`, `company_telephone`, `company_email`, `owner_name`, `owner_mobile`, `owner_email`, `contact_name`, `contact_mobile`, `contact_email`, `is_deactivated`, `created_at`, `updated_at`) VALUES
(4, 'Kaneki\'s', 'World anime, Mati City', '087 201 0693', 'kaneki@gmail.com', 'Kaneki', '2362526598256', 'kaneki@gmail.com', 'Reze', '839046029863485', 'reze@gmail.com', 0, '2026-02-24 11:45:20', '2026-03-04 08:29:01'),
(5, 'Marton\'s', 'Camansi Badas, Mati City', '09559549849', 'martons@gmail.com', 'Marton', '0977238573985', 'marton@gmail.com', 'Marco', '738573759805', 'marco@gmail.com', 0, '2026-02-24 11:46:23', '2026-03-04 08:27:27');

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `gtin` varchar(14) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `name_fr` varchar(255) NOT NULL,
  `description_en` text NOT NULL,
  `description_fr` text NOT NULL,
  `brand` varchar(255) NOT NULL,
  `country_of_origin` varchar(100) NOT NULL,
  `gross_weight` decimal(10,3) NOT NULL DEFAULT 0.000,
  `net_weight` decimal(10,3) NOT NULL DEFAULT 0.000,
  `weight_unit` varchar(20) NOT NULL DEFAULT 'g',
  `image_path` varchar(255) DEFAULT NULL,
  `is_hidden` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `products` (`id`, `company_id`, `gtin`, `name_en`, `name_fr`, `description_en`, `description_fr`, `brand`, `country_of_origin`, `gross_weight`, `net_weight`, `weight_unit`, `image_path`, `is_hidden`, `created_at`, `updated_at`) VALUES
(5, 4, '0987654321234', 'Kaboom', 'Audio Amplifier', 'The kaboom older than the big bang theory is the most kaboom-nest kaboom of all', '2000W 2 Channel Amplifier With Mixer Equalizer USB Bluetooth Fm Radio AV-MP326BT Home Stereo Audio', 'Sony', 'Philippines', 5.503, 5.503, 'kg', 'uploads/products/prod_20260304_083521_e97390ed.jpg', 0, '2026-02-24 11:48:49', '2026-03-04 08:35:21'),
(6, 4, '12345678901234', 'LaptopX', 'lapeytop dw bwabwa', 'basta mao na ni ang laptop nga naay x', 'de lapetop de bwabwa muwahalapen', 'ZXZ', 'India', 0.504, 0.506, 'g', 'uploads/products/prod_20260304_083329_f083889b.jpg', 0, '2026-02-24 13:07:14', '2026-03-04 08:33:29'),
(7, 5, '89528956295623', 'PhoneX', 'PonyweXey', 'mao na ni ang phone nga x nga astang ninduta', 'Mwa la phoney abailabole', 'ZXZ', 'Switzerland', 0.008, 0.004, 'g', 'uploads/products/prod_20260304_083111_0762689d.webp', 0, '2026-03-04 08:31:11', '2026-03-04 08:31:11');

ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_products_gtin` (`gtin`),
  ADD KEY `fk_products_company` (`company_id`),
  ADD KEY `idx_products_gtin` (`gtin`);

ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON UPDATE CASCADE;
COMMIT;


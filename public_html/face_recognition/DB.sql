
INSERT INTO `admin` (`id`, `fullname`, `password`, `email`, `recovery_token`, `expiry_token`, `used_token`, `role`, `admin_quota`, `user_price`) VALUES
(1, 'Admin Admin','$2y$10$N96cn02uRaHvqcREQCG4yuuMXcNPubMiRYhpFU.JSDo1xHPDYf91K', 'admin@admin.com', NULL, NULL, 'N', 'admin', '2', 8.00);


CREATE TABLE `admin` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fullname` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `recovery_token` text,
  `expiry_token` varchar(255) DEFAULT NULL,
  `used_token` varchar(1) DEFAULT 'N',
  `role` varchar(255) NOT NULL DEFAULT 'admin',
  `admin_quota` int(11) NOT NULL,
  `admin_subscription` varchar(4) NOT NULL DEFAULT 'Free',
  `user_price` decimal(10,2) NOT NULL,
  `latitude` decimal(10,8) NOT NULL DEFAULT '0.00000000',
  `longitude` decimal(11,8) NOT NULL DEFAULT '0.00000000',
  `premises` decimal(8,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `app_errors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data` longtext NOT NULL,
  `email` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL,
  `timezone` varchar(255) NOT NULL,
  `errorType` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `faces` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image_path` text NOT NULL,
  `email` varchar(255) NOT NULL,
  `light_version_id` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `light` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `last_action_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_action` varchar(3) NOT NULL,
  `light_version_id` varchar(255) NOT NULL,
  `date_limit` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` varchar(255) NOT NULL,
  `order_status` varchar(255) NOT NULL,
  `order_reference_id` varchar(255) NOT NULL,
  `order_price` decimal(10,2) NOT NULL,
  `order_currency` varchar(10) NOT NULL DEFAULT 'USD',
  `order_payee_email` varchar(255) NOT NULL,
  `order_name` varchar(255) NOT NULL,
  `order_qte` decimal(10,2) NOT NULL,
  `order_payer_firstname` varchar(255) NOT NULL,
  `order_payer_lastname` varchar(255) NOT NULL,
  `order_intent` varchar(255) NOT NULL,
  `order_create_time` varchar(255) NOT NULL,
  `order_raw_json` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_id` varchar(255) DEFAULT NULL,
  `payment_status` varchar(255) DEFAULT NULL,
  `payment_reference_id` varchar(255) DEFAULT NULL,
  `payment_full_name` varchar(255) DEFAULT NULL,
  `payment_address` varchar(255) DEFAULT NULL,
  `payment_amount` decimal(10,2) DEFAULT NULL,
  `payment_captures_id` varchar(255) DEFAULT NULL,
  `payment_captures_status` varchar(255) DEFAULT NULL,
  `payment_captures_reason` varchar(255) DEFAULT NULL,
  `payment_currency_code` varchar(255) DEFAULT NULL,
  `payment_seller_protection` varchar(255) DEFAULT NULL,
  `payment_create_time` varchar(255) DEFAULT NULL,
  `payment_update_time` varchar(255) DEFAULT NULL,
  `payment_raw_json` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `paypal_keys` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sandbox_client_id` varchar(255) NOT NULL,
  `sandbox_secret_id` varchar(255) NOT NULL,
  `livebox_client_id` varchar(255) NOT NULL,
  `livebox_secret_id` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `tracking` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `time` varchar(255) DEFAULT NULL,
  `date` varchar(255) DEFAULT NULL,
  `timestamp` int(11) DEFAULT NULL,
  `timezone` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fullname` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `profile_img` text,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tracking_time_interval` int(3) DEFAULT '10',
  `created_by` varchar(255) NOT NULL,
  `light_version_user` varchar(3) NOT NULL DEFAULT 'No',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



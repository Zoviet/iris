CREATE TABLE `api_keys` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `key` varchar(100) DEFAULT NULL, 
 `user_id` INT DEFAULT NULL,
 `created_at` timestamp NULL DEFAULT NULL,
 `updated_at` timestamp NULL DEFAULT NULL,
 `deleted_at` timestamp NULL DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `users` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `login` varchar(100) DEFAULT NULL, 
 `password` varchar(255) DEFAULT NULL, 
 `first_name` varchar(100) DEFAULT NULL, 
 `last_name` varchar(100) DEFAULT NULL, 
 `data` varchar(1000) DEFAULT NULL,
 `role` varchar(100) DEFAULT NULL, 
 `created_at` timestamp NULL DEFAULT NULL,
 `updated_at` timestamp NULL DEFAULT NULL,
 `deleted_at` timestamp NULL DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

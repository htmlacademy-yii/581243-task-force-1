CREATE SCHEMA `task_force` DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;

USE `task_force`;

CREATE TABLE `task_force`.`users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `last_name` VARCHAR(45) NULL,
  `name` VARCHAR(45) NOT NULL,
  `email` VARCHAR(45) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `age` TINYINT NULL,
  `city_id` INT NULL,
  `user_status` TINYINT NULL DEFAULT 0,
  `birthday_at` DATE NULL,
  `phone` VARCHAR(11) NULL,
  `skype` VARCHAR(255) NULL,
  `messenger` VARCHAR(255) NULL,
  `address` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NULL,
  `last_activity_at` DATETIME NULL,
  `about` TEXT NULL,
  `avatar_id` INT NULL,
  `settings_id` INT NULL,
  `views` INT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `index2` (`email` ASC, `password` ASC));


CREATE TABLE `task_force`.`user_category` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `category_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `index2` (`user_id` ASC, `category_id` ASC));

CREATE TABLE `task_force`.`user_foto` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `file_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `index2` (`user_id` ASC, `file_id` ASC));

CREATE TABLE `task_force`.`tasks` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `category_id` INT NOT NULL,
  `address` VARCHAR(255) NULL,
  `lat` VARCHAR(255) NULL,
  `long` VARCHAR(255) NULL,
  `budget` INT UNSIGNED NULL,
  `expire_at` DATETIME NULL,
  `client_id` INT NOT NULL,
  `executor_id` INT NULL,
  `task_status_id` INT NOT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`));

CREATE TABLE `task_force`.`replies` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `task_id` INT NOT NULL,
  `executor_id` INT NOT NULL,
  `price` INT UNSIGNED NULL,
  `comment` TEXT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`));

CREATE TABLE `task_force`.`task_file` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `task_id` INT NOT NULL,
  `file_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `index2` (`task_id` ASC, `file_id` ASC));

CREATE TABLE `task_force`.`files` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `type` VARCHAR(255) NOT NULL,
  `path` VARCHAR(255) NOT NULL,
  `user_id` INT NOT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`));

  CREATE TABLE `task_force`.`messages` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `task_id` INT NOT NULL,
  `author_id` INT NOT NULL,
  `comment` TEXT NOT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`));

  CREATE TABLE `task_force`.`cities` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `city` VARCHAR(255) NOT NULL,
  `lat` VARCHAR(255) NOT NULL,
  `long` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `status_UNIQUE` (`city` ASC, `long` ASC, `lat` ASC));

  CREATE TABLE `task_force`.`statuses` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `status` VARCHAR(255) NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `status_UNIQUE` (`status` ASC));

  CREATE TABLE `task_force`.`categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NULL,
  `icon` VARCHAR(45) NULL,
  PRIMARY KEY (`id`));

  CREATE TABLE `task_force`.`opinions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `task_id` INT NOT NULL,
  `comment` TEXT NULL,
  `rate` TINYINT NOT NULL,
  `author_id` INT NOT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`));

  CREATE TABLE `task_force`.`favorites` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `favorite_user_id` INT NOT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `index2` (`favorite_user_id` ASC, `user_id` ASC));

  CREATE TABLE `task_force`.`events` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `mesage` TEXT NOT NULL,
  `send_to_email` VARCHAR(255) NULL,
  `send_email_at` DATETIME NULL,
  `view_feed_at` DATETIME NULL,
  PRIMARY KEY (`id`));

  CREATE TABLE `task_force`.`user_settings` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT NULL,
  `new_messages` TINYINT NULL DEFAULT 1,
  `task_action` TINYINT NULL DEFAULT 1,
  `new_response` TINYINT NULL DEFAULT 1,
  `profile_access` TINYINT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`));

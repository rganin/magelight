<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 10.12.12
 * Time: 2:20
 * To change this template use File | Settings | File Templates.
 */
/* @var $this \Magelight\Installer */

$this->getDb()->execute("
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
	`id` INT(10) NULL AUTO_INCREMENT,
	`title` VARCHAR(50) NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB;

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`category` INT(10) NULL DEFAULT NULL,
	`user_id` INT(11) NULL DEFAULT NULL,
	`title` VARCHAR(64) NULL DEFAULT NULL,
	`details` VARCHAR(2048) NULL DEFAULT NULL,
	`loading_required` TINYINT(4) NULL DEFAULT NULL,
	`city_from` VARCHAR(50) NULL DEFAULT NULL,
	`city_to` VARCHAR(50) NULL DEFAULT NULL,
	`address_from` VARCHAR(128) NULL DEFAULT NULL,
	`address_to` VARCHAR(128) NULL DEFAULT NULL,
	`date_move` INT(11) NULL DEFAULT NULL,
	`date_added` INT(11) NULL DEFAULT NULL,
	`max_price` INT(11) NULL DEFAULT NULL,
	`weight` FLOAT NULL DEFAULT NULL,
	`passengers` INT(10) NULL,
	PRIMARY KEY (`id`),
	INDEX `category` (`category`),
	INDEX `user_id` (`user_id`),
	INDEX `weight` (`weight`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=100;
");

$this->getDb()->execute("
INSERT INTO `categories` (`id`, `title`) VALUES (1, 'Мебель и техника');
INSERT INTO `categories` (`id`, `title`) VALUES (2, 'Личные вещи');
INSERT INTO `categories` (`id`, `title`) VALUES (3, 'Квартирный/офисный переезд');
INSERT INTO `categories` (`id`, `title`) VALUES (4, 'Перевозка пассажиров');
INSERT INTO `categories` (`id`, `title`) VALUES (5, 'Обслуживание торжеств');
INSERT INTO `categories` (`id`, `title`) VALUES (6, 'Авто, мото, перегон');
INSERT INTO `categories` (`id`, `title`) VALUES (7, 'Животные');
INSERT INTO `categories` (`id`, `title`) VALUES (8, 'Продукты питания');
INSERT INTO `categories` (`id`, `title`) VALUES (9, 'Спецтехника');
INSERT INTO `categories` (`id`, `title`) VALUES (10, 'Стройматериалы');
INSERT INTO `categories` (`id`, `title`) VALUES (11, 'Вывоз мусора');
INSERT INTO `categories` (`id`, `title`) VALUES (12, 'Доставка из магазина');
INSERT INTO `categories` (`id`, `title`) VALUES (13, 'Документы / Письма');
INSERT INTO `categories` (`id`, `title`) VALUES (14, 'Прочее');
");
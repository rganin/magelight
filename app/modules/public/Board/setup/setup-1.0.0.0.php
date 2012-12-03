<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 02.12.12
 * Time: 2:07
 * To change this template use File | Settings | File Templates.
 */
/* @var $this \Magelight\Installer */

$this->getDb()->execute("
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`is_registered` TINYINT(3) NULL DEFAULT NULL,
	`date_register` INT(11) NULL DEFAULT NULL,
	`openid_provider` VARCHAR(32) NULL DEFAULT NULL,
	`openid_identity` VARCHAR(256) NULL DEFAULT NULL,
	`openid_uid` VARCHAR(96) NULL DEFAULT NULL,
	`name` VARCHAR(50) NULL DEFAULT NULL,
	`photo` VARCHAR(256) NULL DEFAULT NULL,
	`city_id` INT(10) NULL DEFAULT NULL,
	`country_id` INT(10) NULL DEFAULT NULL,
	`city` VARCHAR(50) NULL DEFAULT NULL,
	`country` VARCHAR(50) NULL DEFAULT NULL,
	`email` VARCHAR(96) NULL DEFAULT NULL,
	`email_verified` TINYINT(3) NULL DEFAULT NULL,
	`password` VARCHAR(64) NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	INDEX `openid_provider` (`openid_provider`),
	INDEX `openid_identity` (`openid_identity`(255)),
	INDEX `email` (`email`),
	INDEX `city_id` (`city_id`),
	INDEX `country_id` (`country_id`)
)
COMMENT='User accounts and emails'
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `contacts`;
CREATE TABLE `contacts` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`user_id` INT(10) NULL DEFAULT NULL,
	`type` VARCHAR(10) NULL DEFAULT NULL,
	`content` VARCHAR(150) NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	INDEX `user_id` (`user_id`),
	INDEX `type` (`type`)
)
COMMENT='User contacts'
COLLATE='utf8_general_ci'
ENGINE=InnoDB;

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`parent_id` INT(10) NULL DEFAULT NULL,
	`title` VARCHAR(50) NULL DEFAULT NULL,
	`description` VARCHAR(1024) NULL DEFAULT NULL,
	`icon_class` VARCHAR(256) NULL DEFAULT NULL,
	`attribute_set` INT(10) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	INDEX `parent_id` (`parent_id`)
)
COMMENT='Board categories'
COLLATE='utf8_general_ci'
ENGINE=InnoDB;

CREATE TABLE `attributes` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`type` VARCHAR(10) NULL DEFAULT NULL,
	`name` VARCHAR(32) NULL DEFAULT NULL,
	`front_name` VARCHAR(64) NULL DEFAULT NULL,
	`front_input_type` VARCHAR(32) NULL DEFAULT NULL,
	`front_input_class` VARCHAR(32) NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `name` (`name`),
	INDEX `type` (`type`)
)
COMMENT='Attributes for posts'
COLLATE='utf8_general_ci'
ENGINE=InnoDB;

DROP TABLE IF EXISTS `attribute_sets`;
CREATE TABLE `attribute_sets` (
	`set_id` INT(10) NULL,
	`attribute_id` INT(10) NULL,
	INDEX `set_id` (`set_id`),
	INDEX `attribute_id` (`attribute_id`)
)
COMMENT='Sets of attributes'
COLLATE='utf8_general_ci'
ENGINE=InnoDB;





");

$this->getDb()->execute("

");
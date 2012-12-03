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
<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 10.12.12
 * Time: 23:17
 * To change this template use File | Settings | File Templates.
 */

/* @var $this \Magelight\Installer */

$this->getDb()->execute("
DROP TABLE IF EXISTS `order_geo`;
CREATE TABLE `order_geo` (
	`order_id` INT(10) NULL,
	`latitude_from` DOUBLE NULL,
	`longitude_from` DOUBLE NULL,
	`latitude_to` DOUBLE NULL,
	`longitude_to` DOUBLE NULL,
	`geo_address_from` VARCHAR(128) NULL,
	`geo_address_to` VARCHAR(128) NULL,
	`route_length` FLOAT NULL,
	`route_time` FLOAT NULL,
	`city_from_id` INT NULL,
	`city_to_id` INT NULL,
	`route_google_response` MEDIUMTEXT NULL,
	UNIQUE INDEX `order_id` (`order_id`),
	CONSTRAINT `FK__orders` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB;

");
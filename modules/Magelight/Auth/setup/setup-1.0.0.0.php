<?php
/**
 * Magelight
 *
 * NOTICE OF LICENSE
 *
 * This file is open source and it`s distribution is based on
 * Open Software License (OSL 3.0). You can obtain license text at
 * http://opensource.org/licenses/osl-3.0.php
 *
 * For any non license implied issues please contact rganin@gmail.com
 *
 * DISCLAIMER
 *
 * This file is a part of a framework. Please, do not modify it unless you discard
 * further updates.
 *
 * @version 1.0
 * @author Roman Ganin
 * @copyright Copyright (c) 2012-2015 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
");

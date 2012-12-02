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
        `id` INT(10) NULL,
        `parent_id` INT(10) NULL,
        `title` VARCHAR(50) NULL,
        `description` VARCHAR(1024) NULL,
        `icon` VARCHAR(256) NULL,
        PRIMARY KEY (`id`),
        INDEX `parent_id` (`parent_id`)
    )
    COMMENT='Board categories'
    COLLATE='utf8_general_ci'
    ENGINE=InnoDB;
");

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
        `email` VARCHAR(96) NULL DEFAULT NULL,
        `password` VARCHAR(64) NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        INDEX `openid_provider` (`openid_provider`),
        INDEX `openid_identity` (`openid_identity`(255)),
        INDEX `email` (`email`)
    )
    COMMENT='User accounts and emails'
    COLLATE='utf8_general_ci'
    ENGINE=InnoDB
    AUTO_INCREMENT=2;
");

$this->getDb()->execute("
    DROP TABLE IF EXISTS `contacts`;
    CREATE TABLE `contacts` (
        `id` INT(10) NULL,
        `user_id` INT(10) NULL,
        `type` INT(10) NULL,
        `content` VARCHAR(150) NULL,
        PRIMARY KEY (`id`),
        INDEX `user_id` (`user_id`),
        INDEX `type` (`type`)
    )
    COMMENT='User contacts'
    COLLATE='utf8_general_ci'
    ENGINE=InnoDB;
");


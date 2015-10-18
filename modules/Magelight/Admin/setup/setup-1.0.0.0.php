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
    DROP TABLE IF EXISTS `admin_users`;
    CREATE TABLE `admin_users` (
        `id` INT(10) NULL AUTO_INCREMENT,
        `user_id` INT(10) NULL,
        `rights` VARCHAR(2048) NULL,
        INDEX `user_id` (`user_id`),
        PRIMARY KEY (`id`),
        CONSTRAINT `{$this->getDb()->prepareUniqueTriggerName('users_admin_users')}` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
    )
    COLLATE='utf8_general_ci'
    ENGINE=InnoDB;
");

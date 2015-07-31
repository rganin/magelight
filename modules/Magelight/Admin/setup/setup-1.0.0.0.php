<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category
 * @package
 * @subpackage
 * @author
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
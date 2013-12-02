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
 * @copyright Copyright (c) 2012 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/* @var $this \Magelight\Installer */
$this->getDb()->execute("
DROP TABLE IF EXISTS `visitors`;
CREATE TABLE `visitors` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`time` INT(11) UNSIGNED NULL DEFAULT '0',
	`ip` BIGINT(20) NULL DEFAULT '0',
	`referer` VARCHAR(2048) NULL DEFAULT '',
	`info` VARCHAR(16384) NULL DEFAULT '',
	PRIMARY KEY (`id`),
	INDEX `time` (`time`),
	INDEX `ip` (`ip`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB;
");

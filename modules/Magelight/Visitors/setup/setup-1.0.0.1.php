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
ALTER TABLE `visitors`
	ALTER `info` DROP DEFAULT;
ALTER TABLE `visitors`
	CHANGE COLUMN `info` `info` MEDIUMBLOB NULL AFTER `referer`;
");

$statement = $this->getDb()->execute('SELECT id, info FROM `visitors` WHERE 1;');
foreach ($statement->fetchAll() as $row) {
    $info = json_decode($row['info'], true);
    $newInfo = [];
    foreach ($info as $action) {
        $newInfo[$action['action']] = isset($newInfo[$action['action']]) ? $newInfo[$action['action']] + 1 : 1;
    }
    $newInfo = gzcompress(json_encode($newInfo), 8);
    $this->getDb()->execute("UPDATE `visitors` SET info=? WHERE id=?;", [$newInfo, $row['id']]);
}

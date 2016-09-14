
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
UPDATE `geo_cities` SET `city_name_ru`='Днепр', `city_name_en`='Dnepr', `city_name_ua`='Дніпро' WHERE  `id`=2600;
");

$this->getDb()->execute("
UPDATE `geo_cities` SET `city_name_ru`='Кропивницкий', `city_name_en`='Kropivnitsky', `city_name_ua`='Кропивницький' WHERE  `id`=2845;
");

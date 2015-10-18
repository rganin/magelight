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
 * @version 1.0
 * @author Roman Ganin
 * @copyright Copyright (c) 2012-2015 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

require __DIR__ . '/../core.php';
\Magelight\App::getForgery()->setPreference(\Magelight\App::class, \Magelight\App\Web::class);
\Magelight\App::getInstance()->addModulesDir(__DIR__ . DS . 'modules')
    ->setAppDir(__DIR__)
    ->init()
    ->run();

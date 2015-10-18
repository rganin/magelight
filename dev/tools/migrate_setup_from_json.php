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

$options = getopt("p:a:");

if (!isset($options['p'])) {
    echo 'Usage:' . PHP_EOL;
    echo 'php -f prepare_translations.php -- -p path/to/executed_scripts.json -a path/to/app/root';
    die();
}

require '../../core.php';
\Magelight\App::getForgery()->setPreference(\Magelight\App::class, \Magelight\App\Web::class);
\Magelight\App::getInstance()->addModulesDir(realpath($options['a']) . DS . 'modules')
    ->setAppDir($options['a'])
    ->setDeveloperMode(true)
    ->init();


$jsonFile = realpath($options['p']);
$scripts = json_decode(file_get_contents($jsonFile), true);
$data = [];
foreach ($scripts as $moduleName => $executedModuleScripts) {
    foreach ($executedModuleScripts as $scriptName => $scriptData) {
        $data[] = [
            'module_name' => $moduleName,
            'script_name' => $scriptName,
        ];
    }
}

$installer = \Magelight\Installer::forge();
foreach ($data as $update) {
    $installer->setSetupScriptExecuted($update['module_name'], $update['script_name']);
}

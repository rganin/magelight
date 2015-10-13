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
 * @copyright Copyright (c) 2012 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight\App;

use Magelight\Exception;

class StaticResource extends \Magelight\App
{
    /**
     * Run app
     *
     * @throws \Exception|Exception
     */
    public function run()
    {
        try {
            \Magelight\Event\Manager::getInstance()->dispatchEvent('app_start', []);
            $request = \Magelight\Http\Request::getInstance();
            $resource = $request->getGet('resource');
            $staticDir = realpath(
                $this->getAppDir()
                . DS
                . \Magelight\Config::getInstance()->getConfigString('global/view/published_static_dir', 'pub/static')
            );
            foreach (array_reverse($this->getModuleDirectories()) as $modulesPath) {
                $filename = $modulesPath . DS . $resource;
                $targetFilename = $staticDir . DS . $resource;
                if (file_exists($filename)) {
                    if (!is_dir(dirname($targetFilename))) {
                        mkdir(dirname($targetFilename), 0777, true);
                    }
                    copy($filename, $targetFilename);
                    header('Location: ' . \Magelight\Helpers\UrlHelper::getInstance()->getUrl($resource));
                    break;
                }
            }
        } catch (\Exception $e) {
            \Magelight\Log::getInstance()->add($e->getMessage());
            if ($this->_developerMode) {
                throw $e;
            }
        }
    }
}

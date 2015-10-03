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
     * @param bool $muteExceptions
     * @throws \Exception|Exception
     */
    public function run($muteExceptions = true)
    {
        try {
            $this->fireEvent('app_start', ['muteExceptions' => $muteExceptions]);
            $request = \Magelight\Http\Request::getInstance();
            $this->setRegistryObject('request', $request);
            $resource = $request->getGet('resource');
            $pubStaticDir = $this->config()->getConfigString('global/view/published_static_dir');
            if (empty($pubStaticDir)) {
                $pubStaticDir = 'pub/static';
            }
            $staticDir = realpath($this->getAppDir() . DS . $pubStaticDir);
            foreach (array_reverse($this->getModuleDirectories()) as $modulesPath) {
                $filename = $modulesPath . DS . $resource;
                if (file_exists($filename)) {
                    if (!is_dir(dirname($staticDir . DS . $resource))) {
                        mkdir(dirname($staticDir . DS . $resource), 0777, true);
                    }
                    copy(
                        $filename,
                        $staticDir . DS . $resource
                    );
                    $url = \Magelight\Helpers\UrlHelper::getInstance()->getUrl($resource);
                    header('Location: ' . $url);
                    $this->shutdown();
                    break;
                }
            }
        } catch (\Exception $e) {
            \Magelight\Log::getInstance()->add($e->getMessage());
            if (!$muteExceptions || $this->_developerMode) {
                throw $e;
            }
        }
    }
}

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

namespace Magelight\App;

use Magelight\Exception;

/**
 * Class StaticResource
 * @package Magelight\App
 */
class StaticResource extends \Magelight\App
{
    /**
     * @var array
     */
    protected $mimeTypes = [
        'js'        => 'application/javascript',
        'jsonp'     => 'application/javascript',
        'json'      => 'application/json',
        'css'       => 'text/css',
        'ico'       => 'image/x-icon',
        'gif'       => 'image/gif',
        'png'       => 'image/png',
        'jpg'       => 'image/jpeg',
        'jpeg'      => 'image/jpeg',
        'svg'       => 'image/svg+xml',
        'eot'       => 'application/vnd.ms-fontobject',
        'ttf'       => 'application/x-font-ttf',
        'otf'       => 'application/x-font-otf',
        'woff'      => 'application/x-font-woff',
        'woff2'     => 'application/font-woff2',
        'swf'       => 'application/x-shockwave-flash',
        'zip'       => 'application/zip',
        'gz'        => 'application/x-gzip',
        'gzip'      => 'application/x-gzip',
        'bz'        => 'application/x-bzip2',
        'csv'       => 'text/csv',
        'xml'       => 'application/xml'
    ];

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
                $resource = str_replace('\\/', DS, $resource);
                $filename = $modulesPath . DS . $resource;
                $targetFilename = $staticDir . DS . $resource;

                if (file_exists($filename)) {
                    if (!is_dir(dirname($targetFilename))) {
                        mkdir(dirname($targetFilename), 0777, true);
                    }
                    if (\Magelight\Config::getInstance()->getConfigBool('global/app/developer_mode', false)) {
                        $pathinfo = pathinfo($filename, PATHINFO_EXTENSION);
                        if (isset($this->mimeTypes[$pathinfo])) {
                            $mimeType = $this->mimeTypes[$pathinfo];
                            header('Content-type: ' . $mimeType);
                        }
                        echo file_get_contents($filename);
                        break;
                    }
                    copy($filename, $targetFilename);
                    header('Location: ' . \Magelight\Helpers\UrlHelper::getInstance()->getUrl($resource));
                    break;
                }
            }
        } catch (\Exception $e) {
            \Magelight\Log::getInstance()->add($e->getMessage());
            if ($this->developerMode) {
                throw $e;
            }
        }
    }
}

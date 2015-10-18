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

namespace Magelight\App;

/**
 * Class Web
 * @package Magelight\App
 */
class Web extends \Magelight\App
{
    /**
     * Run app
     *
     * @throws \Exception|\Magelight\Exception
     */
    public function run()
    {
        try {
            \Magelight\Event\Manager::getInstance()->dispatchEvent('app_start', []);
            $request = \Magelight\Http\Request::getInstance();
            $action = \Magelight\Components\Router::getInstance($this)->getAction((string)$request->getRequestRoute());
            $request->appendGet($action['arguments']);
            $this->dispatchAction($action);
        } catch (\Exception $e) {
            \Magelight\Log::getInstance()->add($e->getMessage());
            if ($this->developerMode) {
                throw $e;
            }
        }
    }
}

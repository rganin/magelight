<?php

namespace Magelight\App;

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
            $this->dispatchAction($action, $request);
        } catch (\Exception $e) {
            \Magelight\Log::getInstance()->add($e->getMessage());
            if ($this->_developerMode) {
                throw $e;
            }
        }
    }

}

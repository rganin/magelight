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

namespace Magelight\Event;

use Magelight\Traits\TForgery;

/**
 * Class Manager
 * @package Magelight\Event
 *
 * @method static $this getInstance()
 */
class Manager
{
    use TForgery;

    /**
     * Dispatch application event (executes all observers that were bound to this event)
     *
     * @param string $eventName
     * @param array $arguments
     * @throws \Magelight\Exception
     */
    public function dispatchEvent($eventName, $arguments = [])
    {
        $observers = (array)\Magelight\Config::getInstance()->getConfigSet('global/events/' . $eventName . '/observer');
        if (!empty($observers)) {
            foreach ($observers as $observerClass) {
                $observerClass = explode('::', (string)$observerClass);
                $class = $observerClass[0];
                $method = isset($observerClass[1]) ? $observerClass[1] : 'execute';
                $observer = call_user_func_array([$class, 'forge'], [$arguments]);
                /* @var $observer \Magelight\Observer*/
                if (!method_exists($observer, $method)) {
                    throw new \Magelight\Exception(
                        "Observer '{$class}' method '{$method}' does not exist or is not callable!"
                    );
                }
                $observer->$method();
            }
        }
    }
}

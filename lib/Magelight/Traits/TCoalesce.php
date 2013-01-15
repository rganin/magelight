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
 * @copyright Copyright (c) 2012 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight\Traits;

/**
 * Coalesce trait (implements coalesce methods)
 */
trait TCoalesce
{
    /**
     * Returns first not empty argument or last if all are empty
     *
     * @param mixed $variable1
     * @param mixed $variable2
     * @return mixed
     */
    public function coalesce($variable1, $variable2)
    {
        $arguments = func_get_args();
        foreach ($arguments as $arg) {
            if (!empty($arg)) {
                return $arg;
            }
        }
        return array_pop($arguments);
    }

    /**
     * Returns first argument that passes callback check
     *
     * @param callable $callback - must return bool value
     * @param mixed $variable1
     * @param mixed $variable2
     * @return mixed
     */
    public function coalesceCallback($callback, $variable1, $variable2)
    {
        $arguments = func_get_args();
        array_shift($arguments);
        foreach ($arguments as $arg) {
            if ($callback($arg)) {
                return $arg;
            }
        }
        return array_pop($arguments);
    }
}

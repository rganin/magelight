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

namespace Magelight\Traits;

/**
 * Coalesce trait (implements coalesce methods)
 */
trait TCoalesce
{
    /**
     * Return first not empty argument
     *
     * @param array ...$arguments
     * @return mixed
     */
    public function coalesce(...$arguments)
    {
        foreach ($arguments as $arg) {
            if (!empty($arg)) {
                return $arg;
            }
        }
        return array_pop($arguments);
    }

    /**
     * Return first argument that matches by callback
     *
     * @param callable $callback
     * @param array ...$arguments
     * @return mixed
     */
    public function coalesceCallback(callable $callback, ...$arguments)
    {
        foreach ($arguments as $arg) {
            if ($callback($arg)) {
                return $arg;
            }
        }
        return array_pop($arguments);
    }
}

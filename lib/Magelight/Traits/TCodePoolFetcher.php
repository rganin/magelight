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
 * Trait for fetching code pool of class or instance
 */
trait TCodePoolFetcher
{
    /**
     * Fetch object code pool
     *
     * @return string
     */
    public function getObjectCodePool()
    {
        return array_values(
            array_filter(
                explode(
                    DS,
                    str_replace(
                        \Magelight::app()->getAppDir(),
                        '',
                        (new \ReflectionObject($this))->getFileName())),
                function($el){return !empty($el);}
            )
        )[1];
    }
}
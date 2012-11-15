<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 15.11.12
 * Time: 23:56
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Components;

trait CodePoolFetcher
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

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

namespace Magelight\Components;
use Magelight\Traits\TForgery;

/**
 * Application enabled modules info wrapper
 *
 * @method static Modules getInstance()
 */
class Modules
{
    use TForgery;

    /**
     * Models
     * 
     * @var array
     */
    protected $modules = [];
    
    /**
     * App
     * 
     * @var \Magelight\App
     */
    protected $app = null;

    /**
     * Load modules from file
     * 
     * @param $modulesXmlFilename
     * @return \Magelight\Components\Modules
     */
    public function loadModules($modulesXmlFilename)
    {
        $xml = simplexml_load_file($modulesXmlFilename);
        $modulesLoader = \Magelight\Components\Loaders\Modules::forge($xml);
        $this->modules = $modulesLoader->getActiveModules();
        unset($modulesLoader);
        return $this;
    }
    
    /**
     * Get application modules
     * 
     * @return mixed
     */
    public function getActiveModules()
    {
        return $this->modules;
    }
}

<?php
/**
 * $$name_placeholder_notice$$
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
 * @version $$version_placeholder_notice$$
 * @uthor $$author_placeholder_notice$$
 * @copyright Copyright (c) 2012 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Bike\Components;

class Modules
{
    /**
     * Modules
     * 
     * @var array
     */
    protected $_modules = array();
    
    /**
     * App
     * 
     * @var \Bike\App
     */
    protected $_app = null;
    
    /**
     * Constructor
     * 
     * @param \Bike\App $app
     */
    public function __construct(\Bike\App $app)
    {
        $this->_app = $app;
        $this->loadModules('modules.xml');
    }
    
    /**
     * Load modules from file
     * 
     * @param $modulesXmlFilename
     * @return \Bike\Components\Modules
     */
    public function loadModules($modulesXmlFilename)
    {
        $xml = simplexml_load_file($modulesXmlFilename);
        $modulesLoader = new \Bike\Components\Loaders\Modules($xml);
        $this->_modules = $modulesLoader->getActiveModules();
        unset($modulesLoader);
        return $this;
    }
    
    /**
     * Get application modules
     * 
     * @return mixed
     */
    public function getModules()
    {
        return $this->_modules;
    }
}
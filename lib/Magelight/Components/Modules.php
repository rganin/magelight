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

class Modules
{
    /**
     * Models
     * 
     * @var array
     */
    protected $_modules = [];
    
    /**
     * App
     * 
     * @var \Magelight\App
     */
    protected $_app = null;
    
    /**
     * Constructor
     * 
     * @param \Magelight\App $app
     */
    public function __construct(\Magelight\App $app)
    {
        $this->_app = $app;
        //@todo add modules caching just as caching will be implemented
        $this->loadModules($app->getAppDir() . DS . 'etc' . DS . 'modules.xml');
    }
    
    /**
     * Load modules from file
     * 
     * @param $modulesXmlFilename
     * @return \Magelight\Components\Modules
     */
    public function loadModules($modulesXmlFilename)
    {
        $xml = simplexml_load_file($modulesXmlFilename);
        $modulesLoader = new \Magelight\Components\Loaders\Modules($xml);
        $this->_modules = $modulesLoader->getActiveModules();
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
        return $this->_modules;
    }
}

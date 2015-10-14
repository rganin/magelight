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

namespace Magelight\Components\Loaders;
use Magelight\Traits\TForgery;

/**
 * Modules loader
 */
final class Modules
{
    use TForgery;

    /**
     * Default modules xml node name
     */
    const MODULES_NODE_NAME = 'modules';
    
    /**
     * Models array
     * 
     * @var array
     */
    protected $_modules = [];
    
    /**
     * Loading queue
     * 
     * @var array
     */
    protected $_loadQueue = [];
    
    /**
     * Constructor. Automatically starts modules loading
     * 
     * @param \SimpleXMLElement $modulesXmlConfig
     */
    public function __forge(\SimpleXMLElement $modulesXmlConfig)
    {
        if ($modulesXmlConfig->getName() === self::MODULES_NODE_NAME) {
            foreach ($modulesXmlConfig->children() as $module) {
                if ($module->active) {
                    $this->enqueue($module);
                }
            }
            
            while (!empty($this->_loadQueue)) {
                foreach ($this->_loadQueue as $module) {
                    if ($this->requiredModulesLoaded($module)) {
                        $this->loadModule($module);
                    }
                }
            }
        }
    }
    
    /**
     * Enqueue module for loading
     * 
     * @param \SimpleXMLElement $moduleXml
     * @return \Magelight\Components\Loaders\Modules
     */
    private function enqueue(\SimpleXMLElement $moduleXml)
    {
        $this->_loadQueue[$moduleXml->getName()] = $moduleXml;
        return $this;
    }
    
    /**
     * Load module
     * 
     * @param \SimpleXMLElement $moduleXml
     * @return \Magelight\Components\Loaders\Modules
     * @throws \Magelight\Exception
     */
    private function loadModule(\SimpleXMLElement $moduleXml)
    {
        $module = (array) $moduleXml;
        $module['name'] = $moduleXml->getName();
        if (!isset($module['path'])) {
            $module['path'] = $module['name'];
        }
        $module['path'] = str_replace(['\\','/'], DS, $module['path']);
        
        if (\Magelight\App::getInstance()->isInDeveloperMode() && !$this->moduleExists($module['path'])) {
            throw new \Magelight\Exception('Module "' .  $module['name'] . '" does not exist or not readable.');
        }
        
        $this->_modules[$module['name']] = $module;
        unset($this->_loadQueue[$module['name']]);
        return $this;
    }

    /**
     * Check does module exists in app scope
     *
     * @param string $path
     * @return bool
     */
    private function moduleExists($path)
    {
        $result = false;
        foreach (\Magelight\App::getInstance()->getModuleDirectories() as $modulesDir) {
            $result |= is_readable($modulesDir . DS . $path);
        }
        return $result;
    }
    
    /**
     * Check are the required modules loaded
     * 
     * @param \SimpleXMLElement $moduleXml
     * @return bool
     * @throws \Magelight\Exception
     */
    private function requiredModulesLoaded(\SimpleXMLElement $moduleXml)
    {
        
        $result = true;
        foreach ($moduleXml->xpath('require') as $require) {
            $require = (string) $require;
            if (!isset($this->_loadQueue[$require]) && !isset($this->_modules[$require])) {
                throw new \Magelight\Exception(
                    'Module "' 
                    . $require 
                    . '" required in "' 
                    . $moduleXml->getName() 
                    . '" is not configured for loading.'
                );
            }
            $result &= isset($this->_modules[$require]);
            
        }
        
        return $result;
    }
    
    /**
     * Get loaded modules
     *
     * @return array 
     */
    public function getActiveModules()
    {
        $modules = [];
        foreach ($this->_modules as $name => $module) {
            if ($module['active']) {
                $modules[$name] = $module;
            }
        }
        return $modules;
    }
    
    /**
     * Unset modules and loading queue arrays
     * @return \Magelight\Components\Loaders\Modules
     */
    public function flushArrays()
    {
        $this->_modules = null;
        $this->_loadQueue = null;
        unset($this->_modules);
        unset($this->_loadQueue);
        return $this;
    }
    
    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->flushArrays();
    }
}

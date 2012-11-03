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
 * @version $$version_placeholder_notice$$
 * @author $$author_placeholder_notice$$
 * @copyright Copyright (c) 2012 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight\Components\Loaders;

final class Modules
{
    /**
     * Default modules xml node name
     */
    const MODULES_NODE_NAME = 'modules';
    
    /**
     * Modules array
     * 
     * @var array
     */
    protected $_modules = array();
    
    /**
     * Loading queue
     * 
     * @var array
     */
    protected $_loadQueue = array();
    
    /**
     * Constructor. Automatically starts modules loading
     * 
     * @param \SimpleXMLElement $modulesXmlConfig
     */
    public function __construct(\SimpleXMLElement $modulesXmlConfig)
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
        $module = array(
            'name' => $moduleXml->getName(),
            'active' => (int) $moduleXml->active,
        );
        
        if (\Magelight::app()->isInDeveloperMode() && !$this->moduleExists($module['name'])) {
            throw new \Magelight\Exception('Module "' .  $module['name'] . '" does not exist or not readable.');
        }
        
        $this->_modules[$module['name']] = $module;
        unset($this->_loadQueue[$module['name']]);
        return $this;
    }

    /**
     * Check does module exists in app scope
     *
     * @param string $moduleName
     * @return bool
     */
    private function moduleExists($moduleName)
    {
        $result = false;
        $appDir = \Magelight::app()->getAppDir();
        foreach (['private', 'public'] as $scope) {
            $result |= is_readable($appDir . DS . 'modules' . DS . $scope . DS . $moduleName);
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
        $modules = array();
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
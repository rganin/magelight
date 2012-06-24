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

namespace Bike;

class App
{
    /**
     * Documents array
     * 
     * @var array[\Html\Document]
     */
    protected $_documents = array();
    
    /**
     * Application directory
     * 
     * @var string
     */
    protected $_appDir = './';
    
    /**
     * Configuration
     * 
     * @var array
     */
    protected $_config = array();
    
    /**
     * Modules collection
     * 
     * @var array
     */
    protected $_modules = array();
    
    /**
     * Get application directory
     * 
     * @return string
     */
    public function getAppDir()
    {
        return $this->_appDir;
    }
    
    /**
     * Set application directory
     * 
     * @param $directory
     * @return App
     */
    public function setAppDir($directory)
    {
        $this->_appDir = $directory . DIRECTORY_SEPARATOR;
        return $this;
    }
    
    /**
     * Get document
     * 
     * @param string $index
     * @return \Bike\Html\Document
     */
    public function document($index = 'default')
    {
        if (!isset($this->_documents[$index])) {
            $this->_documents[$index] = new \Bike\Html\Document();
        }
        return $this->_documents[$index];
    }
    
    /**
     * Load modules from file
     * 
     * @param $modulesXmlFilename
     * @return \Bike\App
     */
    public function loadModules($modulesXmlFilename)
    {
        $xml = simplexml_load_file($modulesXmlFilename);
        $modulesLoader = new \Bike\Loaders\Modules($xml);
        $this->_modules = $modulesLoader->getActiveModules();
        var_dump($this->_modules);
        unset($modulesLoader);
        return $this;
    }
    
    
    public function loadConfig($filename)
    {
        return $this;
        
    }
    
    
    
    public function run($muteExceptions = true)
    {
        try {
            $this->loadModules('modules.xml');
            $request = new \Bike\Http\Request();
            var_dump($this);
        } catch (\Bike\Exception $e) {
            \Bike\Log::add($e->getMessage()); 
            if (!$muteExceptions) {
                throw $e;
            }
        } catch (\Exception $e) {
            if (!$muteExceptions) {
                throw $e;
            }
        }
    }
}
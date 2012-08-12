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
 * @author $$author_placeholder_notice$$
 * @copyright Copyright (c) 2012 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Bike;

abstract class View
{
    /**
     * Template string path
     * 
     * @var string|null
     */
    protected $_template = null;
    
    /**
     * Template variables
     * 
     * @var array
     */
    protected $_variables = array();
    
    /**
     * View sections with children blocks
     * 
     * @var array
     */
    protected $_sections = array();
    
    public function render()
    {
        
    }
    
    
    /**
     * Append section content with self
     * 
     * @param string $section
     * @throws Exception
     * @return View
     */
    public function appendSection($section)
    {
        if (!isset($this->_sections[$section])) {
            throw new \Bike\Exception("Section {$section} does not exist in " . get_called_class());
        }
        $this->_sections[$section][] = $this;
        return $this;
    }
    
    /**
     * Prepend section content with self
     * 
     * @param string $section
     * @throws Exception
     * @return View
     */
    public function prependSection($section)
    {
        if (!isset($this->_sections[$section])) {
            throw new \Bike\Exception("Section {$section} does not exist in " . get_called_class());
        }
        $sections = array_reverse($this->_sections[$section]);
        $sections[] = $this;
        $this->_sections[$section] = $sections;
        return $this;
    }
    
    /**
     * Replace section content with self
     * 
     * @param string $section
     * @throws Exception
     * @return View
     */
    public function replaceSection($section)
    {
        if (!isset($this->_sections[$section])) {
            throw new \Bike\Exception("Section {$section} does not exist in " . get_called_class());
        }
        $this->_sections[$section] = array($this);
        return $this;
    }
    
    /**
     * Create section
     * 
     * @param string $sectionName
     * @return View
     * @throws Exception
     */
    public function section($sectionName)
    {
        if (isset($this->_sections[$sectionName])) {
            throw new \Bike\Exception("Section {$sectionName} already exists in " . get_called_class());
        }
        $this->_sections[$sectionName] = array();
        return $this;
    }
    
    /**
     * Set variable value
     * 
     * @param string $varName
     * @param mixed $varValue
     * @return View
     */
    public function set($varName, $varValue)
    {
        $this->_variables[(string) $varName] = $varValue;
        return $this;
    }
    
    /**
     * Link variable value to view internal var
     * 
     * @param string $varName
     * @param mixed $varValue
     * @return View
     */
    public function link($varName, &$varValue)
    {
        $this->_variables[(string) $varName] = &$varValue;
        return $this;
    }
    
    /**
     * Get variable value
     * 
     * @param string $varName
     * @param mixed|null $default
     * @return mixed|null
     */
    public function get($varName, $default = null)
    {
        if (isset($this->_variables[$varName])) {
            return $this->_variables[$varName];
        }
        return $default;
    }
    
    /**
     * Set template
     * 
     * @param $template
     * @return View
     */
    public function setTemplate($template)
    {
        $this->_template = $template;
        return $this;
    }
}
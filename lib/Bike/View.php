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

class View extends \Bike\Prototypes\Overridable
{
    protected $_template = null;
    
    protected $_vars = array();
    
    protected static $_globalVars = array(); 
    
    protected static $_sections = array();
    
    protected static $_sectionsInitialized = false;
    
    public function set($name, $value)
    {
        $this->_vars[$name] = $value;
        return $this;
    }
    
    public function get($name, $default = null)
    {
        return isset($this->_vars[$name]) ? $this->_vars[$name] : $default;
    }
    
    public function setGlobal($name, $value)
    {
        self::$_globalVars[$name] = $value;
        return $this;
    }
    
    public function getGlobal($name, $default = null)
    {
        return isset(self::$_globalVars[$name]) ? self::$_globalVars[$name] : $default;
    }
    
    public function __set($name, $value)
    {
        $this->_vars[$name] = $value;
    }
    
    public function __get($name)
    {
        return isset($this->_vars[$name]) ? $this->_vars[$name] : null;
    }
    
    public function sectionAppend($name, $block)
    {
        self::$_sectionsInitialized = false;
        if (!isset(self::$_sections[$name]) || !is_array(self::$_sections[$name])) {
            return $this->sectionReplace($name, $block);
        }
        self::$_sections[$name][] = $block;
        return $this;
    }
    
    public function sectionPrepend($name, $block)
    {
        self::$_sectionsInitialized = false;
        if (!isset(self::$_sections[$name]) || !is_array(self::$_sections[$name])) {
            return $this->sectionReplace($name, $block);
        }
        self::$_sections = array_unshift(self::$_sections[$name], $block);
        return $this;
    }
    
    public function sectionReplace($name, $block)
    {
        self::$_sectionsInitialized = false;
        self::$_sections[$name] = array($block);
        return $this;
    }
    
    public function sectionDelete($name)
    {
        self::$_sectionsInitialized = false;
        unset(self::$_sections[$name]);
        return $this;
    }
    
    public function initSections()
    {
        foreach (self::$_sections as $name => $section) {
            if (!empty($section)) {
                foreach ($section as $key => $view) {
                    if (!$view instanceof \Bike\View && is_string($view)) {
                        $section[$key] = call_user_func(array($view, 'create'));
                    }
                }
            }
            self::$_sections[$name] = $section;
        }
        self::$_sectionsInitialized = true;
        return $this;
    }
    
    public function toHtml()
    {
        if (!self::$_sectionsInitialized) {
            $this->initSections();
        }
        $class = get_called_class();
        if (empty($this->_template)) {
            throw new \Bike\Exception("Undeclared template in block '{$class}'");
        }
        $this->beforeToHtml();
        $classArray = explode('\\', $class);
        ob_start();
        include($classArray[0] . DS . 'templates' . DS . $this->_template);
        $this->afterToHtml();
        return ob_get_clean();
    }
    
    protected function beforeToHtml()
    {
        return $this;
    }
    
    protected function afterToHtml()
    {
        return $this;
    }
    
    public function section($name)
    {
        $html = '';
        if (!self::$_sectionsInitialized) {
            $this->initSections();
        }
        if (!isset(self::$_sections[$name]) && \Bike::app()->isInDeveloperMode()) {
            throw new \Bike\Exception("Undefined section call - '{$name}' in " . get_called_class());
        }
        if (is_array(self::$_sections[$name])) {
            foreach (self::$_sections[$name] as $sectionBlock) {
                /* @var $sectionBlock \Bike\View */
                $html .= $sectionBlock->toHtml();
            }
        }
        return $html;
    }
}
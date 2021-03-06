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
 * @copyright Copyright (c) 2012-2015 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight\Components\Loaders;
use Magelight\Traits\TForgery;

/**
 * Config loader class
 *
 * @method static $this forge()
 */
class Config
{
    use TForgery;

    /**
     * Configuration
     * 
     * @var \SimpleXmlElement
     */
    protected $config = null;

    /**
     * Load configuration and merge it
     * 
     * @param $filename
     *
     * @return Config
     */
    public function loadConfig($filename)
    {
        $filename = str_replace(['\\', '/'], DS, $filename);
        $xml = simplexml_load_file($filename, 'SimpleXMLElement');
        if (!$this->config instanceof \SimpleXMLElement) {
            $this->config = $xml;
        } else {
            self::mergeConfig($this->config, $xml);
        }
        return $this;
    }

    /**
     * Get module configuration file by module config if exists
     *
     * @param $modulesDir
     * @param $module
     * @return null|string
     */
    public function getModulesConfigFilePath($modulesDir, $module)
    {
        $filename = $modulesDir . DS . $module['path'] . DS . 'etc' . DS . 'config.xml';
        if (is_readable($filename)) {
            return $filename;
        }
        return null;
    }

    /**
     * Merge one xml element to another recursively
     *
     * @static
     * @param \SimpleXMLElement $base
     * @param \SimpleXMLElement $add
     */
    public static function mergeConfig(\SimpleXMLElement $base, \SimpleXMLElement $add)
    {
        foreach ($add->children() as $name => $child) {
            if (!isset($base->$name)
                || (isset($base->$name) && ((bool) $base->attributes()->stackable || $add->attributes()->stackable))
            ) {
                $base->addChild($name);
            } elseif (isset($base->$name) && (bool) $base->attributes()->private) {
                continue;
            }
            self::copyAttributes($base->$name, $add->$name);
            if (!(bool) $base->$name->attributes()->protected) {
                if ((bool) $base->$name->attributes()->stackable_child) {
                    foreach ($base->$name->children() as $firstChild) {
                        /* @var \SimpleXMLElement $firstChild */
                        if (!isset($firstChild->attributes()->stackable)) {
                            $firstChild->addAttribute('stackable', '1');
                        }
                    }
                    foreach ($add->$name->children() as $firstChild) {
                        /* @var \SimpleXMLElement $firstChild */
                        if (!isset($firstChild->attributes()->stackable)) {
                            $firstChild->addAttribute('stackable', '1');
                        }
                    }
                }

                if (!(bool) $base->$name->attributes()->stackable) {
                    $childStr = (string) $add->$name;
                    if ($child->children()->count()) {
                        self::mergeConfig($base->$name, $add->$name);
                    } elseif (!empty($childStr) && !is_array($base->$name)) {
                        if (!(bool) $base->attributes()->stackable) {
                            $base->$name = $add->$name;
                        }
                    }
                } else {
                    foreach ($child->children() as $stackChildName => $stackChildNode) {
                        $node = $base->$name->addChild($stackChildName, $stackChildNode);
                        self::copyAttributes($node, $stackChildNode);
                        self::mergeConfig($node, $stackChildNode);
                    }
                }
            }
        }
    }

    /**
     * Copy xml attributes from one node to another
     *
     * @param \SimpleXMLElement $to
     * @param \SimpleXMLElement $from
     */
    public static function copyAttributes(\SimpleXMLElement $to, \SimpleXMLElement $from)
    {
        foreach ($from->attributes() as $attrName => $attrValue) {
            if (!isset($to->attributes()->$attrName)) {
                $to->addAttribute($attrName, $attrValue);
            } else {
                $to->attributes()->$attrName = $attrValue;
            }
        }
    }

    /**
     * Get loaded Configuration
     * 
     * @return \SimpleXMLElement
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Set config XML
     *
     * @param \SimpleXMLElement $config
     * @return $this
     */
    public function setConfig(\SimpleXMLElement $config = null)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        unset($this->config);
    }
}

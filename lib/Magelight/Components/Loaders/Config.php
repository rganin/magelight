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

class Config
{
    /**
     * Configuration
     * 
     * @var \SimpleXmlElement
     */
    protected $_config = null;

    /**
     * Load configuration and merge it
     * 
     * @param $filename
     *
     * @return Config
     */
    public function loadConfig($filename)
    {
        $xml = simplexml_load_file($filename, 'SimpleXMLElement');
        if (!$this->_config instanceof \SimpleXMLElement) {
            $this->_config = $xml;
        } else {
            self::mergeConfig($this->_config, $xml);
        }
        return $this;
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
            if (!isset($base->$name)) {
                $base->addChild($name, $child);
            }
            if (!(bool) $base->$name->attributes()->private) {
                self::copyAttributes($base->$name, $child);
                if (!(bool) $base->$name->attributes()->protected) {
                    if (!(bool) $base->$name->attributes()->stackable) {
                        if ($child->children()->count()) {
                            self::mergeConfig($base->$name, $add->$name);
                        } else {
                            $base->$name = $child;
                        }
                    } else {
                        foreach ($child->children() as $stackChildName => $stackChildNode) {
                            $node = $base->$name->addChild($stackChildName);
                            self::mergeConfig($node, $stackChildNode);
                        }
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
        return $this->_config;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        unset($this->_config);
    }
}

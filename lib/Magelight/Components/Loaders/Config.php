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
//        $xmlHelper = \Magelight::helper('xml');
//        /* @var \Magelight\Helpers\XmlHelper $xmlHelper*/
//        $this->_config = array_replace_recursive(
//            $this->_config,
//            $xmlHelper->xmlToArray(new \SimpleXMLIterator(file_get_contents($filename)))
//        );
        $xml = simplexml_load_file($filename, 'SimpleXMLElement');
        if (!$this->_config instanceof \SimpleXMLElement) {
            $this->_config = $xml;
        } else {
            self::mergeXml($this->_config, $xml);
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
//    public static function mergeXml(\SimpleXMLElement &$base, \SimpleXMLElement $add)
//    {
//        if ( $add->count() != 0 )
//            $new = $base->addChild($add->getName());
//        else
//            $new = $base->addChild($add->getName(), $add);
//        foreach ($add->attributes() as $a => $b)
//        {
//            $new->addAttribute($a, $b);
//        }
//        if ( $add->count() != 0 )
//        {
//            foreach ($add->children() as $child)
//            {
//                self::mergeXml($new, $child);
//            }
//        }
//    }
    function mergeXml(&$simplexml_to, &$simplexml_from)
    {
        foreach ($simplexml_from->children() as $simplexml_child)
        {
            $simplexml_temp = $simplexml_to->addChild($simplexml_child->getName(), (string) $simplexml_child);
            foreach ($simplexml_child->attributes() as $attr_key => $attr_value)
            {
                $simplexml_temp->addAttribute($attr_key, $attr_value);
            }

            self::mergeXml($simplexml_temp, $simplexml_child);
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
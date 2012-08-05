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

namespace Bike\Helpers;

class XmlHelper extends \Bike\Prototypes\Singleton
{
    /**
     * Content index
     */
    const INDEX_CONTENT = '@content';

    /**
     * Attributes index
     */
    const INDEX_ATTRIBUTES = '@attributes';
    
    public static function xmlToArray(\SimpleXMLIterator $xml, $ns = null){
        $a = array();
        for($xml->rewind(); $xml->valid(); $xml->next()) {
            
            $key = $xml->key();
            
            if (!isset($a[$key])) { 
                $a[$key] = array();
            }
            
            foreach($xml->current()->attributes() as $k => $v) {
                $a[$key][self::INDEX_ATTRIBUTES][$k]=(string)$v;
            }
            
            if($ns) foreach($ns as $name) {
                foreach($xml->current()->attributes($name) as $k=>$v) {
                    $a[$key][self::INDEX_ATTRIBUTES][$k]=(string)$v;
                }
            } 
            
            if($xml->hasChildren()) {
                $a[$key][self::INDEX_CONTENT] = self::xmlToArray($xml->current(), $ns);
            } else {
                $a[$key][self::INDEX_CONTENT] = strval($xml->current());
            }
        }
        return $a;
    } 
}
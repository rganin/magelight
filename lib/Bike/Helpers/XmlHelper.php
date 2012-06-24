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

class XmlHelper
{
    public static function xmlToArray(\SimpleXMLIterator $xml, $ns = null){
        $a = array();
        for($xml->rewind(); $xml->valid(); $xml->next()) {
            $key = $xml->key();
            if (!isset($a[$key])) { 
                $a[$key] = array(); $i=0; 
            } else {
                $i = count($a[$key]);
            }
            $simple = true;
            foreach($xml->current()->attributes() as $k => $v) {
                $a[$key][$i][$k]=(string)$v;
                $simple = false;
            }
            if($ns) foreach($ns as $nid=>$name) {
                foreach($xml->current()->attributes($name) as $k=>$v) {
                    $a[$key][$i][$nid.':'.$k]=(string)$v;
                    $simple = false;
                }
            } 
            if($xml->hasChildren()) {
                if($simple) $a[$key][$i] = self::xmlToArray($xml->current(), $ns);
                else $a[$key][$i]['content'] = self::xmlToArray($xml->current(), $ns);
            } else {
                if($simple) $a[$key][$i] = strval($xml->current());
                else $a[$key][$i]['content'] = strval($xml->current());
            }
            $i++;
        }
        return $a;
    } 
}
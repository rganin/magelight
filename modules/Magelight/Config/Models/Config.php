<?php

namespace Magelight\Config\Models;

use Magelight\Traits\TForgery;

/**
 * Class Config
 * @package Magelight\Config\Models
 *
 * @method static $this getInstance()
 */
class Config extends \Magelight\Config
{

    public function getEditableNodes(\SimpleXMLElement $element, $path = '/', &$targetArray = [])
    {
        $attributes = $this->getElementAttributes($element);
        if (empty((array)$element->children())) {
            if (!empty($attributes['editable'])) {
                $targetArray[$path] = [
                    'attributes' => $attributes,
                    'value' => (string)$element
                ];
            }
        } else {
            /** @var \SimpleXMLElement $child */
            foreach ($element->children() as $child) {
                $this->getEditableNodes($child, $path . '/' . $child->getName(), $targetArray);
            }
        }
        return $targetArray;
    }

    public function getElementAttributes(\SimpleXMLElement $element)
    {
        $result = [];
        foreach ($element->attributes() as $key => $value) {
            $result[(string)$key] = (string)$value;
        }
        return $result;
    }
}

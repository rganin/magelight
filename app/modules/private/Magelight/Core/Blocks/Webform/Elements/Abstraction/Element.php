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

namespace Magelight\Core\Blocks\Webform\Elements\Abstraction;

/**
 * @method static \Magelight\Core\Blocks\Webform\Elements\Abstraction\Element forge()
 */
class Element extends \Magelight\Block
{
    protected $_tag = 'div';

    protected $_empty = false;

    protected $_content = [];

    protected $_attributes = [];

    /**
     * Registered ids
     *
     * @var array
     */
    protected static $_registeredIds = [];

    public function setEmpty($empty = true)
    {
        $this->_empty = $empty;
        return $this;
    }

    public function toHtml()
    {
        if (!empty($this->_template)) {
            return parent::toHtml();
        }
        $html = '<' . $this->_tag . '';
        foreach ($this->_attributes as $name => $attr) {
            $html .= ' ' . $name . '="' . $attr . '"';
        }
        if ($this->_empty) {
            $html .= ' />';
            return $html;
        } else {
            $html .= '>';
        }
        foreach ($this->_content as $content) {
            if ($content instanceof \Magelight\Block) {
                $html .= $content->toHtml();
            } elseif (is_string($content)) {
                $html .= $content;
            }
        }
        $html .= '</' . $this->_tag . '>';
        return $html;
    }

    public function beforeToHtml()
    {
        return $this;
    }

    public function afterToHtml()
    {
        return $this;
    }

    public function setAttribute($name, $value)
    {
        if ($name === 'id') {
            throw new \Magelight\Exception(
                'Direct id assignment is not allowed. Use setId() method to set id attribute.'
            );
        }
        $this->_attributes[$name] = $value;
        return $this;
    }

    public function setClass($class)
    {
        return $this->setAttribute('class', $class);
    }

    public function addClass($class)
    {
        if (!isset($this->_attributes['class'])) {
            $this->_attributes['class'] = '';
        }
        return $this->setAttribute('class', $this->_attributes['class'] . ' ' . $class);
    }

    public function removeClass($class)
    {
        if (!isset($this->_attributes['class'])) {
            $this->_attributes['class'] = '';
        }
        return $this->setAttribute('class', str_replace($class, '', $this->_attributes['class']));
    }

    public function getAttribute($name, $default = null)
    {
        return isset($this->_attributes[$name]) ? $this->_attributes[$name] : $default;
    }

    public function setId($id)
    {
        $id = $this->wrapId($id);
        $this->_attributes['id'] = $id;
        return $this->registerId($id);
    }

    public function getId()
    {
        return $this->_attributes['id'];
    }

    public function addContent($content)
    {
        $this->_content[] = $content;
        return $this;
    }

    protected function registerId($id)
    {
        self::$_registeredIds[$id] = true;
        return $this;
    }

    protected function isIdRegistered($id)
    {
        return isset(self::$_registeredIds[$id]);
    }

    protected function wrapId($id)
    {
        $newId = $id;
        $suffix = 0;
        while (isset(self::$_registeredIds[$newId])) {
            $newId = $id . '-' . $suffix;
            $suffix++;
        }
        return $newId;
    }

    /**
     * @param $labelText
     * @return Element
     */
    public function setContent($labelText)
    {
        return $this->addContent($labelText);
    }

    public function setTag($tag)
    {
        $this->_tag = $tag;
        return $this;
    }
}

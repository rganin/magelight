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

namespace Magelight\Webform\Blocks\Elements\Abstraction;

/**
 * @method static \Magelight\Webform\Blocks\Elements\Abstraction\Element forge()
 */
class Element extends \Magelight\Block
{
    const QUOTATION_DEFAULT = '"';
    const QUOTATION_DOUBLE  = '"';
    const QUOTATION_SINGLE  = '\'';
    const QUOTATION_NONE    = '';

    /**
     * Form pointer
     *
     * @var \Magelight\Webform\Blocks\Form
     */
    protected $_form = null;

    /**
     * Element tag
     *
     * @var string
     */
    protected $_tag = 'div';

    /**
     * Is element empty flag (if true, element is closed with ' />' and has no content)
     *
     * @var bool
     */
    protected $_empty = false;

    /**
     * Element content blocks
     *
     * @var array
     */
    protected $_content = [];

    /**
     * Element attributes
     *
     * @var array
     */
    protected $_attributes = [];

    /**
     * Registered ids
     *
     * @var array
     */
    protected static $_registeredIds = [];

    /**
     * Set element empty flag
     *
     * @param bool $empty
     * @return Element
     */
    public function setEmpty($empty = true)
    {
        $this->_empty = $empty;
        return $this;
    }

    /**
     * Render element to html
     *
     * @return string
     */
    public function toHtml()
    {
        if (!empty($this->_template)) {
            return parent::toHtml();
        }
        $this->beforeToHtml();
        $html = '<' . $this->_tag . ' ' . $this->renderAttributes();
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
        $this->afterToHtml();
        return $html;
    }

    /**
     * Render form attributes as HTML attributes code
     *
     * @return string
     */
    public function renderAttributes()
    {
        $render = '';
        foreach ($this->_attributes as $name => $attr) {
            if (!isset($attr['quotation'])) {
                $attr['quotation'] = self::QUOTATION_DEFAULT;
            }
            $render .= ' ' . $name . '=' . $attr['quotation'] . $attr['value'] . $attr['quotation'];
        }
        return $render;
    }

    /**
     * Before to html handler
     *
     * @return \Magelight\Block|Element
     */
    public function beforeToHtml()
    {
        return $this;
    }

    /**
     * After to html handler
     *
     * @return \Magelight\Block|Element
     */
    public function afterToHtml()
    {
        return $this;
    }

    /**
     * Set element attribute
     *
     * @param string $name
     * @param string $value
     * @param string $quotation
     * @return Element
     * @throws \Magelight\Exception
     */
    public function setAttribute($name, $value, $quotaiton = self::QUOTATION_DEFAULT)
    {
        if ($name === 'id') {
            throw new \Magelight\Exception(
                __('Direct id assignment is not allowed. Use setId() method to set id attribute.')
            );
        }
        $this->_attributes[$name] = ['value' => $value, 'quotation' => $quotaiton];
        return $this;
    }

    /**
     * Set element class
     *
     * @param string $class
     * @return Element
     */
    public function setClass($class)
    {
        return $this->setAttribute('class', $class);
    }

    /**
     * Add element class
     *
     * @param string $class
     * @return Element
     */
    public function addClass($class)
    {
        if (!isset($this->_attributes['class'])) {
            $this->_attributes['class']['value'] = '';
        }
        return $this->setAttribute('class', $this->_attributes['class']['value'] . ' ' . $class);
    }

    /**
     * Remove class from element classes
     *
     * @param string $class
     * @return Element
     */
    public function removeClass($class)
    {
        if (!isset($this->_attributes['class'])) {
            $this->_attributes['class']['value'] = '';
        }
        return $this->setAttribute('class', str_replace($class, '', $this->_attributes['class']['value']));
    }

    /**
     * Get element attribute
     *
     * @param string $name
     * @param mixed $default
     * @return string|mixed
     */
    public function getAttribute($name, $default = null)
    {
        return isset($this->_attributes[$name]['value']) ? $this->_attributes[$name]['value'] : $default;
    }

    /**
     * Set element ID
     *
     * @param string $id
     * @return Element
     */
    public function setId($id)
    {
        $id = $this->wrapId($id);
        $this->_attributes['id']['value'] = $id;
        return $this->registerId($id);
    }

    /**
     * Get element ID
     *
     * @return string
     */
    public function getId()
    {
        return $this->_attributes['id']['value'];
    }

    /**
     * Add element content
     *
     * @param string|Element|\Magelight\Block $content
     * @return Element
     */
    public function addContent($content)
    {
        $this->_content[] = $content;
        return $this;
    }

    /**
     * Register ID globally
     *
     * @param string $id
     * @return Element
     */
    protected function registerId($id)
    {
        self::$_registeredIds[$id] = $this;
        return $this;
    }

    /**
     * Check is ID registered
     * @param string $id
     * @return bool
     */
    protected function isIdRegistered($id)
    {
        return isset(self::$_registeredIds[$id]);
    }

    /**
     * Wrap ID globally to unique value
     *
     * @param string $id
     * @return mixed
     */
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
     * Set element content
     *
     * @param string $content
     * @return Element
     */
    public function setContent($content)
    {
        return $this->addContent($content);
    }

    /**
     * Set element tag
     *
     * @param string $tag
     * @return Element
     */
    public function setTag($tag)
    {
        $this->_tag = $tag;
        return $this;
    }

    /**
     * Set form that owns this element
     *
     * @param \Magelight\Webform\Blocks\Form $form
     * @return Element
     */
    public function setForm(\Magelight\Webform\Blocks\Form $form)
    {
        $this->_form = $form;
        return $this;
    }

    /**
     * Bind element to form
     *
     * @param \Magelight\Webform\Blocks\Form $form
     * @return Element
     */
    public function bindForm(\Magelight\Webform\Blocks\Form $form = null)
    {
        $this->_form = $form;
        foreach ($this->_content as $child) {
            if ($child instanceof \Magelight\Webform\Blocks\Elements\Abstraction\Element) {
                /* @var $child \Magelight\Webform\Blocks\Elements\Abstraction\Element */
                $child->bindForm($this->_form);
            }
        }
        return $this;
    }

    /**
     * Get bound form
     *
     * @return \Magelight\Webform\Blocks\Form|null
     */
    public function getForm()
    {
        return $this->_form;
    }
}
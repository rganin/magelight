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

namespace Magelight\Core\Blocks;

/**
 * @method static $this forge()
 */
class Element extends \Magelight\Block
{
    const QUOTATION_DEFAULT = '"';
    const QUOTATION_DOUBLE  = '"';
    const QUOTATION_SINGLE  = '\'';
    const QUOTATION_NONE    = '';

    /**
     * Element tag
     *
     * @var string
     */
    protected $tag = 'div';

    /**
     * Is element empty flag (if true, element is closed with ' />' and has no content)
     *
     * @var bool
     */
    protected $empty = false;

    /**
     * Element content blocks
     *
     * @var array
     */
    protected $content = [];

    /**
     * Element attributes
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Set element empty flag
     *
     * @param bool $empty
     * @return $this
     */
    public function setEmpty($empty = true)
    {
        $this->empty = $empty;
        return $this;
    }

    /**
     * Render element to html
     *
     * @return string
     */
    public function toHtml()
    {
        if (!empty($this->template)) {
            return parent::toHtml();
        }
        $this->beforeToHtml();
        $html = '<' . $this->tag . ' ' . $this->renderAttributes();
        if ($this->empty) {
            $html .= ' />';
            return $html;
        } else {
            $html .= '>';
        }
        foreach ($this->content as $content) {
            if ($content instanceof \Magelight\Block) {
                $html .= $content->toHtml();
            } elseif (is_string($content)) {
                $html .= $content;
            }
        }
        $html .= '</' . $this->tag . '>';
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
        foreach ($this->attributes as $name => $attr) {
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
     * @return $this
     */
    public function beforeToHtml()
    {
        return $this;
    }

    /**
     * After to html handler
     *
     * @return $this
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
     * @param string $quotaiton
     * @return $this
     */
    public function setAttribute($name, $value, $quotaiton = self::QUOTATION_DEFAULT)
    {
        $this->attributes[$name] = ['value' => $value, 'quotation' => $quotaiton];
        return $this;
    }

    /**
     * Set element class
     *
     * @param string $class
     * @return $this
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
        if (!isset($this->attributes['class'])) {
            $this->attributes['class']['value'] = '';
        }
        return $this->setAttribute('class', $this->attributes['class']['value'] . ' ' . $class);
    }

    /**
     * Remove class from element classes
     *
     * @param string $class
     * @return Element
     */
    public function removeClass($class)
    {
        if (!isset($this->attributes['class'])) {
            $this->attributes['class']['value'] = '';
        }
        return $this->setAttribute('class', str_replace($class, '', $this->attributes['class']['value']));
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
        return isset($this->attributes[$name]['value']) ? $this->attributes[$name]['value'] : $default;
    }

    /**
     * Set element ID
     *
     * @param string $id
     * @return Element
     */
    public function setId($id)
    {
        $this->setAttribute('id', $id);
        return $this;
    }

    /**
     * Get element ID
     *
     * @return string
     */
    public function getId()
    {
        return isset($this->attributes['id']['value']) ? $this->attributes['id']['value'] : null;
    }

    /**
     * Add element content
     *
     * @param string|Element|\Magelight\Block $content
     * @return $this
     */
    public function addContent($content)
    {
        $this->content[] = $content;
        return $this;
    }

    /**
     * Set element content
     *
     * @param string $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = [$content];
        return $this;
    }

    /**
     * Set element tag
     *
     * @param string $tag
     * @return $this
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
        return $this;
    }
}

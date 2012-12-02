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
 * @method static \Magelight\Webform\Blocks\Elements\Abstraction\Field forge()
 * @method \Magelight\Webform\Blocks\Elements\Abstraction\Field setContent()
 */
class Field extends Element
{
    /**
     * Element tag
     *
     * @var string
     */
    protected $_tag = 'input';

    /**
     * Linked row element
     *
     * @var \Magelight\Webform\Blocks\Row|null
     */
    protected $_row = null;

    /**
     * Set element name attribute
     *
     * @param string $name
     * @return Field
     */
    public function setName($name)
    {
        $this->setAttribute('name', $name);
        $this->setId($this->_tag . '-' . preg_replace("([^a-z0-9]+)", '', $name));
        return $this;
    }

    /**
     * Set field value and\or select current option or check the checkbox or radio element
     *
     * @param $value
     * @return Element
     */
    public function setFieldValueFromRequest($value)
    {
        return $this->setValue($value);
    }

    /**
     * Set element value attribute
     *
     * @param string $value
     * @return Element
     */
    public function setValue($value)
    {
        return $this->setAttribute('value', $value);
    }

    /**
     * Get field value
     *
     * @return mixed|string
     */
    public function getValue()
    {
        return $this->getAttribute('value');
    }

    /**
     * Get element name
     *
     * @return mixed|string
     */
    public function getName()
    {
        return $this->getAttribute('name');
    }

    /**
     * Set element placeholder content
     *
     * @param string $placeholderContent
     * @return Element
     */
    public function setPlaceholder($placeholderContent)
    {
        return $this->setAttribute('placeholder', $placeholderContent);
    }

    /**
     * Before to thml render event
     *
     * @return \Magelight\Block|Element|Field
     */
    public function beforeToHtml()
    {
        if ($this->_form instanceof \Magelight\Webform\Blocks\Form) {
            $this->setAttribute('name', $this->_form->wrapName($this->getAttribute('name')));
        }
        return parent::beforeToHtml();
    }

    /**
     * Clone field to array
     *
     * @param int $count
     * @return array
     */
    public function cloneToArray($count = 1)
    {
        $clones = [];
        for ($i = 0; $i < $count; $i++) {
            $clone = clone $this;
            /* @var $clone \Magelight\Webform\Blocks\Elements\Abstraction\Field */
            $clone->setName($clone->getName() . '[' . $i . ']');
            $clones[] = $clone;
        }
        return $clones;
    }

    /**
     * Set row that contains this field
     *
     * @param \Magelight\Webform\Blocks\Row $row
     * @return Field
     */
    public function setRow(\Magelight\Webform\Blocks\Row $row)
    {
        $this->_row = $row;
        return $this;
    }

    /**
     * Get row element that contains this field
     *
     * @return \Magelight\Webform\Blocks\Row|null
     */
    public function getRow()
    {
        return $this->_row;
    }
}

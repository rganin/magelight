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

namespace Magelight\Webform\Blocks\Elements;

/**
 * @method static \Magelight\Webform\Blocks\Elements\Checkbox forge()
 */
class LabeledCheckbox extends Label
{
    /**
     * Checkbox object
     *
     * @var Checkbox
     */
    protected $_checkbox = null;

    /**
     * Forgery constructor
     */
    public function __forge()
    {
        $this->_checkbox = Checkbox::forge();
        $this->addContent($this->_checkbox);
        $this->addClass('checkbox');
        $this->addContent('&nbsp;');
    }

    /**
     * Set checkbox name
     *
     * @param string $name
     * @return LabeledCheckbox
     */
    public function setName($name)
    {
        $this->_checkbox->setName($name);
        $this->setFor($this->_checkbox->getId());
        return $this;
    }

    /**
     * Get checkbox ID
     *
     * @return string
     */
    public function getId()
    {
        return $this->_checkbox->getId();
    }

    /**
     * Set checkbox checked
     *
     * @return LabeledCheckbox
     */
    public function setChecked()
    {
        $this->_checkbox->setChecked();
        return $this;
    }

    /**
     * Get checkbox object
     *
     * @return Checkbox
     */
    public function getCheckboxObject()
    {
        return $this->_checkbox;
    }

    /**
     * Set checkbox id
     *
     * @param string $id
     * @return LabeledCheckbox
     */
    public function setCheckboxId($id)
    {
        $this->_checkbox->setId($id);
        $this->setFor($this->_checkbox->getId());
        return $this;
    }

    /**
     * Set checkbox value
     *
     * @param string $value
     * @return LabeledCheckbox
     */
    public function setCheckboxValue($value)
    {
        $this->_checkbox->setValue($value);
        return $this;
    }

    /**
     * Set checkbox class
     *
     * @param string $class
     * @return LabeledCheckbox
     */
    public function setCheckboxClass($class)
    {
        $this->_checkbox->setClass($class);
        return $this;
    }

    /**
     * Add checkbox class
     *
     * @param string $class
     * @return LabeledCheckbox
     */
    public function addCheckboxClass($class)
    {
        $this->_checkbox->addClass($class);
        return $this;
    }

    /**
     * Remove checkbox class
     *
     * @param string $class
     * @return LabeledCheckbox
     */
    public function removeCheckboxClass($class)
    {
        $this->_checkbox->removeClass($class);
        return $this;
    }
}

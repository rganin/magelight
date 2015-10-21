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

namespace Magelight\Webform\Blocks\Elements;

/**
 * @method static \Magelight\Webform\Blocks\Elements\LabeledCheckbox forge()
 */
class LabeledCheckbox extends Checkbox
{
    /**
     * Checkbox object
     *
     * @var Checkbox
     */
    protected $checkbox = null;

    /**
     * Label object
     *
     * @var Label
     */
    protected $label = null;

    /**
     * Forgery constructor
     */
    public function __forge()
    {
        $this->checkbox = Checkbox::forge()->removeClass('form-control');
        $this->label = Label::forge()->removeClass('control-label');
        $this->label->addContent($this->checkbox);
        $this->label->addClass('checkbox');
    }

    /**
     * Render element HTML
     *
     * @return string
     */
    public function toHtml()
    {
        return $this->label->toHtml();
    }

    /**
     * Add label content
     *
     * @param Abstraction\Element|string $content
     * @return LabeledCheckbox
     */
    public function addContent($content)
    {
        $this->label->addContent($content);
        return $this;
    }

    /**
     * Set checkbox name
     *
     * @param string $name
     * @return LabeledCheckbox
     */
    public function setName($name)
    {
        $this->checkbox->setName($name);
        $this->label->setFor($this->checkbox->getId());
        return $this;
    }

    /**
     * Get checkbox ID
     *
     * @return string
     */
    public function getId()
    {
        return $this->checkbox->getId();
    }

    /**
     * Set checkbox checked
     *
     * @return LabeledCheckbox
     */
    public function setChecked()
    {
        $this->checkbox->setChecked();
        return $this;
    }

    /**
     * Get checkbox object
     *
     * @return Checkbox
     */
    public function getCheckboxObject()
    {
        return $this->checkbox;
    }

    /**
     * Set checkbox id
     *
     * @param string $id
     * @return LabeledCheckbox
     */
    public function setCheckboxId($id)
    {
        $this->checkbox->setId($id);
        $this->label->setFor($this->checkbox->getId());
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
        $this->checkbox->setValue($value);
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
        $this->checkbox->setClass($class);
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
        $this->checkbox->addClass($class);
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
        $this->checkbox->removeClass($class);
        return $this;
    }

    /**
     * Set field value
     *
     * @param $value
     * @return LabeledCheckbox
     */
    public function setFieldValueFromRequest($value)
    {
        $this->checkbox->setChecked();
        $this->checkbox->setFieldValueFromRequest($value);
        return $this;
    }

    /**
     * Bind form to element
     *
     * @param \Magelight\Webform\Blocks\Form $form
     * @return LabeledCheckbox
     */
    public function bindForm(\Magelight\Webform\Blocks\Form $form = null)
    {
        $this->checkbox->bindForm($form);
        return $this;
    }
}

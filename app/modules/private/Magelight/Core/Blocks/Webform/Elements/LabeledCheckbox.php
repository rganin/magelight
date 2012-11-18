<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 18.11.12
 * Time: 12:31
 * To change this template use File | Settings | File Templates.
 */
namespace Magelight\Core\Blocks\Webform\Elements;

/**
 * @method static \Magelight\Core\Blocks\Webform\Elements\Checkbox forge()
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
     * Constructor
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
     * @param $name
     * @return LabeledCheckbox
     */
    public function setName($name)
    {
        $this->_checkbox->setName($name);
        $this->setFor($this->_checkbox->getId());
        return $this;
    }

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
     * @param $id
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
     * @param $value
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
     * @param $class
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
     * @param $class
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
     * @param $class
     * @return LabeledCheckbox
     */
    public function removeCheckboxClass($class)
    {
        $this->_checkbox->removeClass($class);
        return $this;
    }
}

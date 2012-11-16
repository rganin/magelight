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

namespace Magelight\Core\Blocks\Form;

abstract class AbstractFormElement extends \Magelight\Block
{
    protected $_elements = [];

    protected $_attributes = [];

    public function toHtml()
    {

    }

    public function beforeToHtml()
    {

    }

    public function afterToHtml()
    {

    }

    public function setAttribute($name, $value)
    {
        $this->_attributes[$name] = $value;
        return $this;
    }

    public function setClass($class)
    {
        return $this->setAttribute('class', $class);
    }

    public function addClass($class)
    {
        return $this->setAttribute('class', $this->_attributes['class'] . ' ' . $class);
    }

    public function removeClass($class)
    {
        return $this->setAttribute('class', str_replace($class, '', $this->_attributes['class']));
    }

    public function setId($id)
    {
        return $this->setAttribute('id', $id);
    }
}

<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category
 * @package
 * @subpackage
 * @author
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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

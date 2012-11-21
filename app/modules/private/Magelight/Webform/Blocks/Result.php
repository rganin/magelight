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

namespace Magelight\Webform\Blocks;

/**
 * @method static \Magelight\Webform\Blocks\Result forge()
 */
class Result extends Elements\Abstraction\Element
{
    protected $_tag = 'div';

    public function __forge()
    {
        $this->addClass('alert');
    }

    public function setErrorClass()
    {
        return $this->addClass('alert-error');
    }

    public function setWarningClass()
    {
        return $this->addClass('alert-warning');
    }

    public function setInfoClass()
    {
        return $this->addClass('alert-info');
    }

    public function setSuccessClass()
    {
        return $this->addClass('alert-success');
    }
}
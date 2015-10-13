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

namespace Magelight\Webform\Blocks\Elements;

/**
 * @method static \Magelight\Webform\Blocks\Elements\Captcha forge($renderUrl = null)
 */
class Captcha extends Abstraction\Field
{
    protected $_template = 'Magelight/Webform/templates/webform/elements/captcha.phtml';

    protected $_captcha = null;

    public function __forge($renderUrl = null)
    {
        $this->_captcha = \Magelight\Webform\Models\Captcha\Captcha::forge();

        if (empty($renderUrl)) {
            $this->_captcha->loadCodeFromSession()->generate()->saveCodeToSession();
            $this->_captcha->save();
        }
        $this->set('image_url', !empty($renderUrl) ? $renderUrl : $this->url($this->_captcha->getSavedFileName()));
        $this->addClass('form-group');
    }
}
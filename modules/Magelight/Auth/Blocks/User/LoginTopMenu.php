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

namespace Magelight\Auth\Blocks\User;

/**
 * @method static \Magelight\Auth\Blocks\User\Register forge()
 */
class LoginTopMenu extends \Magelight\Block
{
    /**
     * Login form template
     *
     * @var string
     */
    protected $_template = 'Magelight/Auth/templates/user/login-form.phtml';

    /**
     * Init override
     *
     * @return \Magelight\Block|void
     */
    public function initBlock()
    {
        $this->sectionReplace('ulogin-widget', \Magelight\Auth\Blocks\UloginWidget::forge()->setConfigIndex('default'));
    }
}

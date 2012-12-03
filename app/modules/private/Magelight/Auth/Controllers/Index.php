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

namespace Magelight\Auth\Controllers;


class Index extends \Magelight\Controller
{
    /**
     * Before execute handler
     */
    public function beforeExecute()
    {
        $this->_view = \Magelight\Core\Blocks\Document::forge();
        $this->_view->sectionAppend('body', \Magelight\Core\Blocks\Body::forge());
        $this->_view->sectionAppend('login-menu-option', \Magelight\Auth\Blocks\User\LoginTopMenu::forge());
    }

    public function loginAction()
    {

    }

    public function registerAction()
    {
        $this->_view->sectionReplace('content', \Magelight\Auth\Blocks\User\Register::forge());
        $this->renderView();
    }

    public function serviceloginAction()
    {
        $s = file_get_contents('http://ulogin.ru/token.php?token='
            . $this->request()->getPost('token')
            . '&host='
            . $this->server()->getCurrentDomain());
        $userData = json_decode($s, true);
        $user = \Magelight\Auth\Models\User::forge()->authorizeViaUlogin($userData);
        if (!$user) {
            $user = \Magelight\Auth\Models\User::forge()->createViaUlogin(
                $userData,
                $this->url($this->app()->getConfig('global/auth/avatar/noavatar_url'))
            );
        }
        if ($user) {
            $this->session()->set('user_id', $user->id);
            $this->redirect($this->url($this->app()->getConfig('global/auth/register/success_url')));
        } else {
            $this->redirect($this->url($this->app()->getConfig('global/auth/register/openauth_error')));
        }
    }

    public function logoutAction()
    {
        $this->session()->unsetData('user_id');
        $this->redirect($this->url($this->app()->getConfig('global/auth/register/success_url')));
    }
}

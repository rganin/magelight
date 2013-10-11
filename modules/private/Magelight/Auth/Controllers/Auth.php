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

/**
 * Auth controller
 */
class Auth extends \Magelight\Core\Controllers\BaseController
{
    /**
     * Login action handler
     */
    public function loginAction()
    {
        $contentBlock = \Magelight\Auth\Blocks\User\Login::forge();

        $form = $contentBlock->_getLoginForm();
        if (!$form->isEmptyRequest() && $form->validate()) {
            /* @var $user \Magelight\Auth\Models\User */
            if (!$user = \Magelight\Auth\Models\User::orm()
                ->whereEq('email', $form->getFieldValue('email'))
                ->whereEq('password', md5($form->getFieldValue('password')))
                ->fetchModel()
            ) {
                $form->addResult('Incorrect password or user with specified email is not registered');
            } else {
                $this->session()->set('user_id', $user->id);
                $this->redirect($this->url($this->app()->getConfig('global/auth/urls/success_url')));
            }
        }
        $this->_view->sectionAppend('content', $contentBlock);
        $this->_view->sectionReplace('login-user-main-form', $form);
        $this->renderView();
    }

    /**
     * Register action handler
     */
    public function registerAction()
    {
        $this->_view->setTitle('Register new user');

        $content = \Magelight\Auth\Blocks\User\Register::forge();
        $form = $content->_getRegForm();

        if (!$form->isEmptyRequest()) {
            if ($form->validate()) {
                if (\Magelight\Auth\Models\User::findBy('email', $form->getFieldValue('email'))) {
                    $form->addResult("User with email {$form->getFieldValue('email')} is already registered!");
                } else {
                    $user = \Magelight\Auth\Models\User::forge($form->getRequestFields(), true);
                    $user->password = md5($user->password);
                    $user->save(true);
                    $this->session()->set('user_id', $user->id);
                    $this->redirect($this->url($this->app()->getConfig('global/auth/urls/success_url')));
                }
            }
        }
        $this->_view->sectionReplace('register-user-form', $form);
        $this->_view->sectionReplace('content', $content);
        $this->renderView();
    }

    /**
     * Render captcha image action
     */
    public function render_captchaAction()
    {
        \Magelight\Webform\Models\Captcha\Captcha::forge()->generate()->saveCodeToSession()->render();
    }

    /**
     * Remind password action handler
     */
    public function remindpassAction()
    {
        $contentBlock = \Magelight\Auth\Blocks\User\ForgotPassword::forge();
        $form = $contentBlock->_getForgotPasswordForm();
        if (!$form->isEmptyRequest() && $form->validate()) {
            /* @var $user \Magelight\Auth\Models\User */
            if (!$user = \Magelight\Auth\Models\User::findBy('email', $form->getFieldValue('email'))) {
                $form->addResult('No user registred with ' . $form->getFieldValue('email') . ' email.');
            } else {
                $newPassword = substr(md5(rand(0, 999999999)), 0, 6);
                $user->password = md5($newPassword);
                $user->save(true);
                mail(
                    $user->email,
                    'Password recovery for' . $this->url(''),
                    "Your new password is: {$this->url('')}:
                        {$newPassword}

                    Try to remember it :)",
                    "From: " . $this->app()->getConfig('global/auth/robot_email')
                );
                $form->addResult('Your new password is sent to your email', 'alert-success');
            }
        }
        $this->_view->sectionReplace('forgot-password-form', $form);
        $this->_view->sectionReplace('content', $contentBlock);
        $this->renderView();
    }

    /**
     * Login via uLogin service action handler
     */
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
            $this->redirect($this->url($this->app()->getConfig('global/auth/urls/success_url')));
        } else {
            $this->redirect($this->url($this->app()->getConfig('global/auth/urls/openauth_error')));
        }
    }

    /**
     * Logout action handler
     */
    public function logoutAction()
    {
        $this->session()->unsetData('user_id');
        $this->redirect($this->url($this->app()->getConfig('global/auth/urls/success_url')));
    }
}
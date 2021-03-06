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

        $form = $contentBlock->getLoginForm();
        if (!$form->isEmptyRequest() && $form->validate()) {
            /* @var $user \Magelight\Auth\Models\User */
            if (!$user = \Magelight\Auth\Models\User::orm()
                ->whereEq('email', $form->getFieldValue('email'))
                ->whereEq('password', md5($form->getFieldValue('password')))
                ->fetchModel()
            ) {
                $form->addResult(__('Incorrect password or user with specified email is not registered'));
            } else {
                $this->session()->set('user_id', $user->id);
                $this->redirect(
                    $this->url(
                        \Magelight\Config::getInstance()->getConfig('global/auth/urls/success_url')
                    )
                );
            }
        }
        $this->view->sectionAppend('content', $contentBlock);
        $this->view->sectionReplace('login-user-main-form', $form);
        $this->renderView();
    }

    /**
     * Register action handler
     */
    public function registerAction()
    {
        $this->view->setTitle(__('Register new user'));

        $content = \Magelight\Auth\Blocks\User\Register::forge();
        $form = $content->getRegForm();

        if (!$form->isEmptyRequest()) {
            if ($form->validate()) {
                if (\Magelight\Auth\Models\User::findBy('email', $form->getFieldValue('email'))) {
                    $form->addResult(__("User with email %s is already registered!", [$form->getFieldValue('email')]));
                } else {
                    $user = \Magelight\Auth\Models\User::forge($form->getRequestFields(), true);
                    $user->password = md5($user->password);
                    $user->date_register = time();
                    $user->is_registered = 1;
                    $user->save(true);
                    $this->session()->set('user_id', $user->id);
                    $this->redirect(
                        $this->url(
                            \Magelight\Config::getInstance()->getConfig('global/auth/urls/success_url')
                        )
                    );
                }
            }
        }
        $this->view->sectionReplace('register-user-form', $form);
        $this->view->sectionReplace('content', $content);
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
        $form = $contentBlock->getForgotPasswordForm();
        if (!$form->isEmptyRequest() && $form->validate()) {
            /* @var $user \Magelight\Auth\Models\User */
            if (!$user = \Magelight\Auth\Models\User::findBy('email', $form->getFieldValue('email'))) {
                $form->addResult(__('No user registred with %s email.', [$form->getFieldValue('email')]));
            } else {
                $newPassword = substr(md5(rand(0, 999999999)), 0, 6);
                $user->password = md5($newPassword);
                $user->save(true);
                mail(
                    $user->email,
                    __('Password recovery for %s', [$this->url('')]),
                    __("Your new password is: %s.

                        Try to remember it :)", [$newPassword]),

                    "From: " . \Magelight\Config::getInstance()->getConfig('global/auth/robot_email')
                );
                $form->addResult(__('Your new password is sent to your email'), 'alert-success');
            }
        }
        $this->view->sectionReplace('forgot-password-form', $form);
        $this->view->sectionReplace('content', $contentBlock);
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
            . \Magelight\Http\Server::getInstance()->getCurrentDomain());
        $userData = json_decode($s, true);
        $user = \Magelight\Auth\Models\User::forge()->authorizeViaUlogin($userData);
        if (!$user) {
            $user = \Magelight\Auth\Models\User::forge()->createViaUlogin(
                $userData,
                $this->url(\Magelight\Config::getInstance()->getConfig('global/auth/avatar/noavatar_url'))
            );
        }
        if ($user) {
            $this->session()->set('user_id', $user->id);
            $this->redirect($this->url(\Magelight\Config::getInstance()->getConfig('global/auth/urls/success_url')));
        } else {
            $this->redirect($this->url(\Magelight\Config::getInstance()->getConfig('global/auth/urls/openauth_error')));
        }
    }

    /**
     * Logout action handler
     */
    public function logoutAction()
    {
        $this->session()->unsetData('user_id');
        $this->redirect($this->url(\Magelight\Config::getInstance()->getConfig('global/auth/urls/success_url')));
    }

    /**
     * User profile action
     */
    public function userAction()
    {
        $userProfileBlock = \Magelight\Auth\Blocks\UserProfile::forge();
        if (!$userProfileBlock->get('user', false)) {
            $this->forward('no_route');
        }
        $userProfileBlock->sectionAppend('content', $userProfileBlock);
        $this->renderView();
    }
}

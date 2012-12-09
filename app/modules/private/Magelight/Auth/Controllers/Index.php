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

use \Magelight\Webform\Blocks\Form as Form;
use \Magelight\Webform\Blocks\Fieldset as Fieldset;
use \Magelight\Webform\Blocks\Elements as Elements;

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

    /**
     * Get registration form
     *
     * @return \Magelight\Webform\Blocks\Form
     */
    protected function _getRegForm()
    {
        $form = Form::forge()->setHorizontal()->setConfigs('regform', $this->url('register'));
        $fieldset = Fieldset::forge();
        $fieldset->addRowField(Elements\Input::forge()->setName('name'), 'Имя');
        $fieldset->addRowField(Elements\Input::forge()->setName('email'), 'E-Mail');
        $fieldset->addRowField(Elements\PasswordInput::forge()->setName('password'), 'Пароль');
        $fieldset->addRowField(Elements\PasswordInput::forge()->setName('passconf'), 'Подтверждение пароля');
        $fieldset->addRowField(Elements\ReCaptcha::forge(), null, 'Введите слова на картинке');
        return $form->addFieldset($fieldset)
            ->createResultRow()
            ->addButtonsRow(Elements\Button::forge()->setContent('Зарегистрироваться')->addClass('btn-primary'))
            ->loadFromRequest($this->request())->setValidator($this->_getRegFormValidator());
    }

    /**
     * Get login form
     *
     * @return \Magelight\Webform\Blocks\Form
     */
    protected function _getLoginForm()
    {
        $form = Form::forge()->setHorizontal()->setConfigs('login-form', $this->url('login'));
        $fieldset = Fieldset::forge();
        $fieldset->addRowField(Elements\Input::forge()->setName('email'), 'E-Mail');
        $fieldset->addRowField(Elements\PasswordInput::forge()->setName('password'), 'Пароль');
        return $form->addFieldset($fieldset)
            ->createResultRow()
            ->addButtonsRow([
                Elements\Button::forge()->setContent('Войти')->addClass('btn-primary'),
                Elements\Abstraction\Element::forge()->setTag('a')->setAttribute('href', $this->url('remindpass'))
                    ->setContent('Забыли пароль?')->setClass('btn')
            ])
            ->loadFromRequest($this->request())->setValidator($this->_getLoginFormValidator());
    }

    /**
     * Get for for password recovery page
     *
     * @return \Magelight\Webform\Blocks\Form
     */
    protected function _getForgotPasswordForm()
    {
        $form = Form::forge()->setHorizontal()->setConfigs('login-form', $this->url('remindpass'));
        $fieldset = Fieldset::forge();
        $fieldset->addRowField(Elements\Input::forge()->setName('email'), 'E-Mail');

        $validator = \Magelight\Webform\Models\Validator::forge();
        $validator->fieldRules('email')->email()->setCustomError('Введите существующий e-mail адрес!');

        return $form->addFieldset($fieldset)
            ->createResultRow()
            ->addButtonsRow([Elements\Button::forge()->setContent('Выслать новый пароль')->addClass('btn-primary')])
            ->loadFromRequest($this->request())->setValidator($validator);
    }

    /**
     * Get form validator
     *
     * @return \Magelight\Webform\Models\Validator
     */
    protected function _getRegFormValidator()
    {
        $validator = \Magelight\Webform\Models\Validator::forge();
        $validator->fieldRules(\Magelight\Webform\Models\Captcha\ReCaptcha::CHALLENGE_INDEX)
            ->reCaptcha()->setCustomError('Слова с картинки введены неверно');
        $validator->fieldRules('password', 'Пароль')
            ->minLength(3)->setCustomError('Пароль должен содержать не менее 3 букв')->chainRule()
            ->maxLength(32)->setCustomError('Пароль Слишком длинный')->chainRule();

        $validator->fieldRules('passconf', 'Подтверждение пароля')
            ->equals($this->request()->getPost('regform')['passconf'])
            ->setCustomError('Пароль и подтверждение не совпадают');

        $validator->fieldRules('name')
            ->minLength(3)->setCustomError('Имя должно быть не менее 3 букв')->chainRule()
            ->maxLength(32)->setCustomError('Имя должно быть не длинее 32 букв')->chainRule()
            ->pregMatch('/[a-z0-9а-я]*/i')->setCustomError('Имя содержит недопустимые символы');

        $validator->fieldRules('email')->email()->setCustomError('Введите существующий e-mail адрес!');

        return $validator;
    }

    /**
     * Get login form validator
     *
     * @return \Magelight\Webform\Models\Validator
     */
    protected function _getLoginFormValidator()
    {
        $validator = \Magelight\Webform\Models\Validator::forge();
        $validator->fieldRules('email')->email()->setCustomError('Введите e-mail адрес!');
        $validator->fieldRules('password', 'Пароль')->required()->setCustomError('Введите пароль');
        return $validator;
    }

    /**
     * Login action handler
     */
    public function loginAction()
    {
        $form = $this->_getLoginForm();
        if (!$form->isEmptyRequest() && $form->validate()) {
            /* @var $user \Magelight\Auth\Models\User */
            if (!$user = \Magelight\Auth\Models\User::orm()
                ->whereEq('email', $form->getFieldValue('email'))
                ->whereEq('password', md5($form->getFieldValue('password')))
                ->fetchModel()
            ) {
                var_dump($user);
                $form->addResult('Неправильный пароль или пользователя с таким e-mail не существует');
            } else {
                $this->session()->set('user_id', $user->id);
                $this->redirect($this->url($this->app()->getConfig('global/auth/register/success_url')));
            }
        }
        $this->_view->sectionAppend('content', \Magelight\Auth\Blocks\User\Login::forge());
        $this->_view->sectionReplace('login-user-main-form', $form);
        $this->renderView();
    }

    /**
     * Register action handler
     */
    public function registerAction()
    {
        $form = $this->_getRegForm();
        $form->validate();
        $this->_view->sectionReplace('register-user-form', $form);
        $this->_view->sectionReplace('content', \Magelight\Auth\Blocks\User\Register::forge());
        $this->renderView();
    }

    /**
     * Remind password action handler
     */
    public function remindpassAction()
    {
        $form = $this->_getForgotPasswordForm();
        if (!$form->isEmptyRequest() && $form->validate()) {
            /* @var $user \Magelight\Auth\Models\User */
            if (!$user = \Magelight\Auth\Models\User::findBy('email', $form->getFieldValue('email'))) {
                $form->addResult('Пользователь с таким e-mail не существует');
            } else {
                $newPassword = substr(md5(rand(0, 999999999)), 0, 6);
                $user->password = md5($newPassword);
                $user->save(true);
                mail(
                    $user->email,
                    'Восстановление пароля на сайте' . $this->url(''),
                    "Ваш новый пароль для входа на сайт {$this->url('')}:
                        {$newPassword}

                    Постарайтесь больше не забывать его :)",
                    "From: " . $this->app()->getConfig('global/auth/robot_email')
                );
                $form->addResult('Новый пароль был отправлен на указанный адрес', 'alert-success');
            }
        }
        $this->_view->sectionReplace('forgot-password-form', $form);
        $this->_view->sectionReplace('content', \Magelight\Auth\Blocks\User\ForgotPassword::forge());
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
            $this->redirect($this->url($this->app()->getConfig('global/auth/register/success_url')));
        } else {
            $this->redirect($this->url($this->app()->getConfig('global/auth/register/openauth_error')));
        }
    }

    /**
     * Logout action handler
     */
    public function logoutAction()
    {
        $this->session()->unsetData('user_id');
        $this->redirect($this->url($this->app()->getConfig('global/auth/register/success_url')));
    }
}

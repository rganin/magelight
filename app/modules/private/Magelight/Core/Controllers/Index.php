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

namespace Magelight\Core\Controllers;
use \Magelight\Core\Blocks\Webform\Form as Form;
use \Magelight\Core\Blocks\Webform\Fieldset as Fieldset;
use \Magelight\Core\Blocks\Webform\Elements as Elements;
use \Magelight\Core\Blocks\Webform\Row as Row;

class Index extends \Magelight\Controller
{

    /**
     * Before execute handler
     */
    public function beforeExecute()
    {
        $this->_view = \Magelight\Core\Blocks\Document::forge();
        $this->_view->sectionAppend('body', \Magelight\Core\Blocks\Body::forge());
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_view->set('title', 'Welcome');
        $this->_view->sectionAppend('content', \Magelight\Core\Blocks\Content::forge());
        \Magelight\Core\Blocks\Document::getFromRegistry()->addMeta(['name' => 'description', 'content' => '123']);
         $this->renderView();
    }

    /**
     * No route action
     */
    public function no_routeAction()
    {
        $this->_view->set('title', 'Page not found');

        $block = \Magelight\Core\Blocks\Error::forge();
        /* @var $block \Magelight\Core\Blocks\Error */

        $block->setTemplate(\Magelight\Core\Blocks\Error::TEMPLATE_404);
        $this->_view->sectionReplace('content', $block);
        $this->app()->log('404 - not found ' . $this->request()->getRequestRoute());
        $this->renderView();
    }

    public function loginAction()
    {
        $this->_view->set('title', 'Log in');

        $form = Form::forge()->setConfigs('login', 'auth');
        $fieldset = Fieldset::forge()->setLegend('Login to app')
            ->addRowField(Elements\Input::forge()->setType('text')->setName('login'), 'Login')
            ->addRowField(Elements\Input::forge()->setType('password')->setName('password'), 'Password')
        ;

        $form->addFieldset($fieldset);
        $form->addButton(Elements\Button::forge()->setType('submit')->setContent('Login'));
        $this->_view->sectionReplace('content', $form);
        $this->renderView();
    }

    public function authAction()
    {
        var_dump($this->_request);
    }

    public function registerAction()
    {
        $form = Form::forge()->setConfigs('register', 'adduser')->setHorizontal();
        $fieldset = Fieldset::forge()->setLegend('Register new user')
            ->addRowField(Elements\Input::forge()->setName('login'),
            'Login',
            'Your login (5-20 characters)')
            ->addRowField(Elements\Input::forge()->setName('email'),
            'Email',
            'Please enter a valid email')
            ->addRowField(Elements\PasswordInput::forge()->setName('password'),
            'Password',
            'Enter your password')
            ->addRowField(Elements\PasswordInput::forge()->setName('passconf'),
            'Confirm password',
            'Confirm your password')
            ->addRowField(Elements\InputMasked::forge()
                ->setName('phone')->setMask('+99 999 999-99-99'),
            'Phone',
            'Please enter your phone')
            ->addRowField(Elements\ReCaptcha::forge()->setName('challenge'), 'Enter captcha')
            ->addRowField(Elements\Button::forge()
                ->setType('submit')
                ->addClass('btn-success')
                ->setContent('Register')
            )
        ;

        $form->addFieldset($fieldset);
        $this->_view->set('title', 'Register new user');
        $this->_view->sectionReplace('content', $form);
        $this->renderView();
    }

    public function adduserAction()
    {
        var_dump($this->_request);
    }
}

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


//        $db = $this->app()->db();
//        $orm = new \Magelight\Dbal\Db\Mysql\Orm('users', 'id', 'Users', $db);
//        var_dump($orm->selectFields(['DISTINCT name', 'password AS pass', 'email'])->whereEq('name', 'Admin')->fetchAll());
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
//        $webform = \Magelight\Core\Blocks\Webform\Webform::forge();
        /* @var $form \Magelight\Core\Blocks\Webform\Form */

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
//        $webform = \Magelight\Core\Blocks\Webform\Webform::forge();
        /* @var $form \Magelight\Core\Blocks\Webform\Form */
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
            ->addRowField(Elements\LabeledCheckbox::forge()->setName('subscribe')->setContent('Subscribe newsletter'))
            ->addRowField([Elements\LabeledRadio::forge()->setName('whatever')->setContent('Simple account'),
                           Elements\LabeledRadio::forge()->setName('whatever')->setContent('Paid account')],
            'Select Account Type')
            ->addRowField(Elements\Select::forge()->importOptions(
                    [
                        [
                            'label' => 'Free plans',
                            'options' => [
                                ['value' => 1, 'title' => 'Wooden']
                            ],
                        ],
                        [
                            'label' => 'Paid plans',
                            'options' => [
                                ['value' => 2, 'title' => 'Silver (10$/month)'],
                                ['value' => 3, 'title' => 'Gold (50$/month)', 'selected' => true],
                                ['value' => 4, 'title' => 'Platinum (100$/month)'],
                            ]
                        ]
                    ]
                )->setName('whatever'), 'Select paid plan')
            ->addRowField(Elements\FilePretty::forge()->setName('avatars[]'), 'Upload your photo')
            ->addRowField(Elements\Textarea::forge()->setName('about')->setValue('About me')
                ->setClass('span6')->setAttribute('rows', 6), 'About you')
        ;
        $form->addFieldset($fieldset);
        $form->addButton(Elements\Button::forge()->setType('submit')->addClass('btn-warning')->setContent('Register me'));
        $this->_view->set('title', 'Register new user');
        $this->_view->sectionReplace('content', $form);
        $this->renderView();
    }
}

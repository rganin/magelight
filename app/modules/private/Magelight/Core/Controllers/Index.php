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
    }

    /**
     * Index action
     */
    public function indexAction()
    {
//        $data = [
//            'login' => '',
//            'date' => '2012-12-12',
//            'age' => 28,
//            'weight' => 80,
//            'url' => 'http://magelight.com',
//        ];
//        $validator = \Magelight\Webform\Models\Validator::forge();
//        $validator->fieldRule('login')->title('"Username"')->required()->minLength(4);
//        $validator->fieldRule('url')->title('"User website"')->url();
//        if (!$validator->check($data)) {
//            var_dump($validator->getErrors());
//        }

        print_r($this->app()->getConfig('global'));
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
        $this->renderView();
    }

    public function authAction()
    {
        var_dump($this->_request);
    }

    public function registerAction()
    {
        $form = Form::forge()->setConfigs('regform', 'register')->setHorizontal();
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
            ->addRowField(Elements\InputMasked::forge()->setName('phone')->setMask('+99 999 999-99-99'),
            'Phone',
            'Please enter your phone')
            ->addRowField(Elements\LabeledCheckbox::forge()->setName('agree-rules')->addContent('Agree with rules'))
            ->addRowField(Elements\ReCaptcha::forge()->setName('challenge'), 'Enter captcha')
            ->addRowField(Elements\FilePretty::forge()->setName('photo'), 'Upload photo')
            ->addContent(\Magelight\Webform\Blocks\Result::forge()->setErrorClass()->setContent('Test result'))
            ->addRowField(Elements\Button::forge()
                ->setType('submit')
                ->setContent('Register')->addClass('btn-primary')
            )
        ;

        $form->addFieldset($fieldset)->loadFromRequest($this->request());
        $this->_view->set('title', 'Register new user');
        $this->_view->sectionReplace('content', $form);
        $this->renderView();
    }

    public function adduserAction()
    {
        var_dump($this->request());
    }
}

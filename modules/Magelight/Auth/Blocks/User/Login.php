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
 * @copyright Copyright (c) 2013 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight\Auth\Blocks\User;

use \Magelight\Webform\Blocks\Form as Form;
use \Magelight\Webform\Blocks\Fieldset as Fieldset;
use \Magelight\Webform\Blocks\Elements as Elements;

/**
 * @method static $this forge()
 */
class Login extends \Magelight\Block
{
    protected $template = 'Magelight/Auth/templates/user/login.phtml';

    /***
     * Init overide
     *
     * @return \Magelight\Block|void
     */
    public function initBlock()
    {
        $this->sectionReplace(
            'ulogin-widget-register',
            \Magelight\Auth\Blocks\UloginWidget::forge()->setConfigIndex('register')
        );
    }

    /**
     * Get login form
     *
     * @return \Magelight\Webform\Blocks\Form
     */
    public function getLoginForm()
    {
        $form = Form::forge()->setConfigs(
            'login-form',
            $this->url(\Magelight\Config::getInstance()->getConfigString('global/auth/urls/login_url'))
        );
        $fieldset = Fieldset::forge();
        $fieldset->addRowField(Elements\Input::forge()->setName('email'), __('E-Mail'));
        $fieldset->addRowField(Elements\PasswordInput::forge()->setName('password'), __('Password'));
        return $form->addFieldset($fieldset)
            ->createResultRow(true)
            ->addButtonsRow([
            Elements\Button::forge()->setContent(__('Enter'))->addClass('btn-primary'),
            Elements\Abstraction\Element::forge()->setTag('a')->setAttribute('href', $this->url('auth/remindpass'))
                ->setContent(__('Remind password'))->setClass('btn')
        ])
            ->loadFromRequest(\Magelight\Http\Request::getInstance())->setValidator($this->getLoginFormValidator());
    }

    /**
     * Get login form validator
     *
     * @return \Magelight\Webform\Models\Validator
     */
    public function getLoginFormValidator()
    {
        $validator = \Magelight\Webform\Models\Validator::forge();
        $validator->fieldRules('email')->required()->chainRule()->email()->setCustomError('Enter correct e-mail');
        $validator->fieldRules('password', __('Password'))->required()->setCustomError('Enter password');
        return $validator;
    }
}

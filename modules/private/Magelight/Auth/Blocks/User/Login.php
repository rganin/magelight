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
 * @method static \Magelight\Auth\Blocks\User\Login forge()
 */
class Login extends \Magelight\Block
{
    protected $_template = 'Magelight/Auth/templates/user/login.phtml';

    /***
     * Init overide
     *
     * @return \Magelight\Block|void
     */
    public function init()
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
    public function _getLoginForm()
    {
        $config = \Magelight::app()->config();
        $form = Form::forge()->setHorizontal()->setConfigs(
            'remindpass-form',
            $this->url($config->getConfigString('global/auth/urls/login_url'))
        );
        $fieldset = Fieldset::forge();
        $fieldset->addRowField(Elements\Input::forge()->setName('email'), 'E-Mail');
        $fieldset->addRowField(Elements\PasswordInput::forge()->setName('password'), 'Password');
        return $form->addFieldset($fieldset)
            ->createResultRow(true)
            ->addButtonsRow([
            Elements\Button::forge()->setContent('Enter')->addClass('btn-primary'),
            Elements\Abstraction\Element::forge()->setTag('a')->setAttribute('href', $this->url('remindpass'))
                ->setContent('Remind password')->setClass('btn')
        ])
            ->loadFromRequest(\Magelight\Http\Request::getInstance())->setValidator($this->_getLoginFormValidator());
    }

    /**
     * Get login form validator
     *
     * @return \Magelight\Webform\Models\Validator
     */
    public function _getLoginFormValidator()
    {
        $validator = \Magelight\Webform\Models\Validator::forge();
        $validator->fieldRules('email')->email()->setCustomError('Enter correct e-mail');
        $validator->fieldRules('password', 'Пароль')->required()->setCustomError('Enter password');
        return $validator;
    }
}

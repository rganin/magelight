<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 09.12.12
 * Time: 21:20
 * To change this template use File | Settings | File Templates.
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

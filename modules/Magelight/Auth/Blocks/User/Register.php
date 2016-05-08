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
 * @method static \Magelight\Auth\Blocks\User\Register forge()
 */
class Register extends \Magelight\Block
{
    /**
     * Register form template
     *
     * @var string
     */
    protected $template = 'Magelight/Auth/templates/user/register.phtml';

    /**
     * Forgery constructor
     */
    public function __forge()
    {
        $this->sectionReplace('ulogin-widget-register',
            \Magelight\Auth\Blocks\UloginWidget::forge()->setConfigIndex('register')
        );
    }

    /**
     * Get registration form
     *
     * @return \Magelight\Webform\Blocks\Form
     */
    public function getRegForm()
    {
        $form = Form::forge()->setConfigs('regform', $this->url('auth/register'));
        $fieldset = Fieldset::forge();
        $fieldset->addRowField(Elements\Input::forge()->setName('name'), __('Name'));
        $fieldset->addRowField(Elements\Input::forge()->setName('email'), __('E-Mail'));
        $fieldset->addRowField(Elements\PasswordInput::forge()->setName('password'), __('Password'));
        $fieldset->addRowField(Elements\PasswordInput::forge()->setName('passconf'),
            __('Confirm password'));
        $fieldset->addRowField(
            Elements\Captcha::forge(
                $this->url(\Magelight\Config::getInstance()->getConfigString('global/auth/urls/render_captcha_url'))
            )->setName('captcha'),
            __('Enter protection code')
        );
        return $form->addFieldset($fieldset)
            ->createResultRow(true)
            ->addButtonsRow(Elements\Button::forge()->setContent(__('Register'))->addClass('btn-primary'))
            ->loadFromRequest()->setValidator($this->getRegFormValidator())->validateOnFront();
    }

    /**
     * Get form validator
     *
     * @return \Magelight\Webform\Models\Validator
     */
    public function getRegFormValidator()
    {
        $validator = \Magelight\Webform\Models\Validator::forge();
        $validator->fieldRules('captcha')
            ->validatePermanent()->captcha()->setCustomError(__('Protection code is incorrect'));

        $validator->fieldRules('password', __('Password'))
            ->required()->chainRule()
            ->minLength(3)->chainRule()
            ->maxLength(32)->chainRule();

        $validator->fieldRules('passconf', __('Password confirmation'))->required()->chainRule()
            ->equals(\Magelight\Http\Request::getInstance()->getPost('regform')['password'], __('entered password'));

        $validator->fieldRules('name')
            ->required()->chainRule()
            ->minLength(3)->chainRule()
            ->maxLength(255)->chainRule()
            ->pregMatch('/[a-z0-9а-я]*/i');

        $validator->fieldRules('email')->required()->chainRule()->email();

        $validator->setErrorsLimit(1);
        return $validator;
    }
}

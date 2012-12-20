<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 02.12.12
 * Time: 22:47
 * To change this template use File | Settings | File Templates.
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
    protected $_template = 'Magelight/Auth/templates/user/register.phtml';

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
    public function _getRegForm()
    {
        $form = Form::forge()->setHorizontal()->setConfigs('regform', $this->url('register'));
        $fieldset = Fieldset::forge();
        $fieldset->addRowField(Elements\Input::forge()->setName('name')->setClass('span9'), 'Name');
        $fieldset->addRowField(Elements\Input::forge()->setName('email')->setClass('span9'), 'E-Mail');
        $fieldset->addRowField(Elements\PasswordInput::forge()->setName('password')->setClass('span9'), 'Password');
        $fieldset->addRowField(Elements\PasswordInput::forge()->setName('passconf')->setClass('span9'),
            'Confirm password');
        $fieldset->addRowField(Elements\ReCaptcha::forge(), null, 'Enter protection code');
        return $form->addFieldset($fieldset)
            ->createResultRow(true)
            ->addButtonsRow(Elements\Button::forge()->setContent('Register')->addClass('btn-primary'))
            ->loadFromRequest()->setValidator($this->_getRegFormValidator());
    }

    /**
     * Get form validator
     *
     * @return \Magelight\Webform\Models\Validator
     */
    public function _getRegFormValidator()
    {
        $validator = \Magelight\Webform\Models\Validator::forge();
        $validator->fieldRules(\Magelight\Webform\Models\Captcha\ReCaptcha::CHALLENGE_INDEX)
            ->validatePermanent()->reCaptcha()->setCustomError('Protection code is incorrect');

        $validator->fieldRules('password', 'Password')
            ->required()->chainRule()
            ->minLength(3)->chainRule()
            ->maxLength(32)->chainRule();

        $validator->fieldRules('passconf', 'Password confirmation')->required()->chainRule()
            ->equals(\Magelight\Http\Request::forge()->getPost('regform')['password'], 'entered password');

        $validator->fieldRules('name')
            ->required()->chainRule()
            ->minLength(3)->chainRule()
            ->maxLength(32)->chainRule()
            ->pregMatch('/[a-z0-9а-я]*/i');

        $validator->fieldRules('email')->required()->chainRule()->email();

        $validator->setErrorsLimit(10000);
        return $validator;
    }
}

<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 09.12.12
 * Time: 22:29
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Auth\Blocks\User;

use \Magelight\Webform\Blocks\Form as Form;
use \Magelight\Webform\Blocks\Fieldset as Fieldset;
use \Magelight\Webform\Blocks\Elements as Elements;

/**
 * @method static \Magelight\Auth\Blocks\User\ForgotPassword forge()
 */
class ForgotPassword extends \Magelight\Block
{
    protected $_template = 'Magelight/Auth/templates/user/forgot-password.phtml';

    /**
     * Get for for password recovery page
     *
     * @return \Magelight\Webform\Blocks\Form
     */
    public function _getForgotPasswordForm()
    {
        $config = \Magelight::app()->config();
        $form = Form::forge()->setHorizontal()->setConfigs(
            'login-form',
            $this->url($config->getConfigString('global/auth/urls/forgot_password_url'))
        );
        $fieldset = Fieldset::forge();
        $fieldset->addRowField(Elements\Input::forge()->setName('email'), 'E-Mail');

        $validator = \Magelight\Webform\Models\Validator::forge();
        $validator->fieldRules('email')->email()->setCustomError('Please enter a valid e-mail!');

        return $form->addFieldset($fieldset)
            ->createResultRow()
            ->addButtonsRow([Elements\Button::forge()->setContent('Send new password')->addClass('btn-primary')])
            ->loadFromRequest(\Magelight\Http\Request::getInstance())->setValidator($validator);
    }
}

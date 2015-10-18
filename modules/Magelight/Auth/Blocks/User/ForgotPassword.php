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
 * @method static \Magelight\Auth\Blocks\User\ForgotPassword forge()
 */
class ForgotPassword extends \Magelight\Block
{
    /**
     * Template to render
     *
     * @var string
     */
    protected $template = 'Magelight/Auth/templates/user/forgot-password.phtml';

    /**
     * Get for for password recovery page
     *
     * @return \Magelight\Webform\Blocks\Form
     */
    public function getForgotPasswordForm()
    {
        $form = Form::forge()->setHorizontal()->setConfigs(
            'remindpass-form',
            $this->url(\Magelight\Config::getInstance()->getConfigString('global/auth/urls/forgot_password_url'))
        );
        $fieldset = Fieldset::forge();
        $fieldset->addRowField(Elements\Input::forge()->setName('email'), __('E-Mail', [], 1, 'default'));

        $validator = \Magelight\Webform\Models\Validator::forge();
        $validator->fieldRules('email')->email()->setCustomError(__("Please enter a valid e-mail!", 1));

        return $form->addFieldset($fieldset)
            ->createResultRow(true)
            ->addButtonsRow([Elements\Button::forge()->setContent(__('Send new password'))->addClass('btn-primary')])
            ->loadFromRequest()->setValidator($validator)->validateOnFront();
    }
}

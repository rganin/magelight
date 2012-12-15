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

namespace Magelight\Core\Blocks;
use \Magelight\Webform\Blocks\Form as Form;
use \Magelight\Webform\Blocks\Fieldset as Fieldset;
use \Magelight\Webform\Blocks\Elements as Elements;

class Top extends \Magelight\Block
{
    protected $_template = 'Magelight/Core/templates/top.phtml';

    public function getLoginFormHtml()
    {
        $form = Form::forge()->setConfigs('login-form', 'auth')->setHorizontal();
        $fieldset = Fieldset::forge()
            ->addRowField(Elements\Input::forge()->setType('text')->setName('login'), 'Login')
            ->addRowField(Elements\Input::forge()->setType('password')->setName('password'), 'Password')
            ->addRowField(Elements\Button::forge()->setType('submit')->setContent('Login')->addClass('btn-primary'));
        $form->addFieldset($fieldset);
        return $form->toHtml();
    }
}

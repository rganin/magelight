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

    /***
     * Init overide
     *
     * @return \Magelight\Block|void
     */
    public function init()
    {
        \Magelight\Core\Blocks\Document::getFromRegistry()->sectionReplace(
            'ulogin-widget-register',
            \Magelight\Auth\Blocks\UloginWidget::forge()->setConfigIndex('register')
        );
    }

    /**
     * Get register form block
     *
     * @return \Magelight\Webform\Blocks\Form
     */
    public function getRegisterForm()
    {
        $form = Form::forge()->setHorizontal()->setConfigs('regform', 'register');
        $fieldset = Fieldset::forge();
        $fieldset->addRowField(Elements\Input::forge()->setName('regform[name]'), 'Имя');
        $fieldset->addRowField(Elements\Input::forge()->setName('regform[email]'), 'E-Mail');
        $fieldset->addRowField(Elements\PasswordInput::forge()->setName('regform[password]'), 'Пароль');
        $fieldset->addRowField(Elements\PasswordInput::forge()->setName('regform[passconf]'), 'Подтверждение пароля');
        $fieldset->addRowField(Elements\ReCaptcha::forge(), null, 'Введите слова на картинке');
        $fieldset->addRowField(Elements\Button::forge()->setContent('Зарегистрироваться')->addClass('btn-primary'));
        return $form->addFieldset($fieldset)->loadFromRequest(new \Magelight\Http\Request());
    }

    /**
     * Get register form HTML
     *
     * @return string
     */
    public function getRegisterFormHtml()
    {
        return $this->getRegisterForm()->toHtml();
    }
}

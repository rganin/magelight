<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 01.12.12
 * Time: 18:50
 * To change this template use File | Settings | File Templates.
 */

namespace Board\Controllers;

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
        $this->_view->sectionAppend('body', \Board\Blocks\Body::forge());
        $this->_view->sectionAppend('content', \Board\Blocks\Home::forge());
        $this->_view->sectionAppend('login-menu-option', \Magelight\Auth\Blocks\User\LoginTopMenu::forge());
    }

    public function indexAction()
    {
//        var_dump((array) \Magelight::app()->config()->getConfig('global/auth/ulogin/instances/' . 'default/options'));
//        $this->app()->log(__METHOD__);
//        $country = \Magelight\Geo\Models\Country::find(2);
//        var_dump($country->getCountryIdByName('Ukraine'));
        $this->renderView();
    }

    public function loginAction()
    {

    }
//
//    public function registerAction()
//    {
//        $this->_view->sectionReplace('content', \Board\Blocks\User\Register::forge());
//
//        $form = Form::forge()->setHorizontal()->setConfigs('regform', 'register');
//        $fieldset = Fieldset::forge();
//        $fieldset->addRowField(Elements\Input::forge()->setName('regform[name]'), 'Имя');
//        $fieldset->addRowField(Elements\Input::forge()->setName('regform[email]'), 'E-Mail');
//        $fieldset->addRowField(Elements\PasswordInput::forge()->setName('regform[password]'), 'Пароль');
//        $fieldset->addRowField(Elements\PasswordInput::forge()->setName('regform[passconf]'), 'Подтверждение пароля');
//        $fieldset->addRowField(Elements\ReCaptcha::forge(), null, 'Введите слова на картинке');
//        $fieldset->addRowField(Elements\Button::forge()->setContent('Зарегистрироваться')->addClass('btn-primary'));
//        $form->addFieldset($fieldset)->loadFromRequest($this->request());
//        $this->_view->sectionReplace('regform', $form);
//        $this->renderView();
//    }

//    public function serviceloginAction()
//    {
//        $s = file_get_contents('http://ulogin.ru/token.php?token='
//            . $this->request()->getPost('token')
//            . '&host='
//            . $this->server()->getCurrentDomain());
//        $userData = json_decode($s, true);
//        $user = \Board\Models\User::forge()->authorizeViaUlogin($userData);
//        if (!$user) {
//            $user = \Board\Models\User::forge()->createViaUlogin(
//                $userData,
//                $this->url('/modules/public/Board/static/img/no-avatar.png')
//            );
//        }
//        if ($user) {
//            $this->session()->set('user_id', $user->id);
//            $this->redirect($this->url('/'));
//        } else {
//            $this->redirect($this->url('/openauth-error'));
//        }
//    }
//
//    public function logoutAction()
//    {
//        $this->session()->unsetData('user_id');
//        $this->redirect($this->url('/'));
//    }
}

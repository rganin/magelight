<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 10.12.12
 * Time: 13:30
 * To change this template use File | Settings | File Templates.
 */

namespace Cargo\Controllers;

/**
 * @property  \Magelight\Core\Blocks\Document $_view
 */
class Orders extends \Magelight\Controller
{
    /**
     * Before execute handler
     */
    public function beforeExecute()
    {
        $this->_view = \Magelight\Core\Blocks\Document::forge();
        $this->_view->sectionAppend('body', \Cargo\Blocks\Body::forge());
        $this->_view->sectionAppend('login-menu-option', \Magelight\Auth\Blocks\User\LoginTopMenu::forge());
    }

    public function addorderAction()
    {
        $this->_view->setTitle('Заказать перевозку');

        $orderForm = \Cargo\Blocks\Order\OrderForm::forge()
            ->setConfigs('order', $this->url('addorder'))
            ->setHorizontal()
            ->setValidator($this->_getOrderValidator())->loadFromRequest($this->request());

        $cities = \Magelight\Geo\Models\City::orm()
            ->selectFields(['city_name_ru'])
            ->whereEq('country_id', 2)
            ->fetchColumn(false);
        $orderForm->set('cities', json_encode($cities));

        $userData = \Magelight\Auth\Models\User::find($this->session()->get('user_id'));
        if ($userData) {
            $orderForm->set('client_name', $userData->name);
            $orderForm->set('client_email', $userData->email);
            $phone = \Magelight\Auth\Models\Contact::orm()
                ->selectFields(['content'])
                ->whereEq('type', 'phone')
                ->whereEq('user_id', $userData->id)
                ->fetchFirstColumnElement();
            $orderForm->set('client_phone', $phone);
        } else {

        }
        $categories = \Cargo\Models\Category::orm()->fetchAll();
        $orderForm->set('categories', $categories);
        if (!$orderForm->isEmptyRequest() && $orderForm->validate()) {
            $data = $orderForm->getRequestFields();
            unset($data['client']);
            \Cargo\Models\Order::forge($data, true)->save();
        }
        $this->_view->sectionReplace('content', $orderForm);
        $this->renderView();
    }

    protected function _getOrderValidator()
    {
        $validator = \Magelight\Webform\Models\Validator::forge();
        $validator->fieldRules('category')
            ->required()->setCustomError('Укажите категорию');

        $validator->fieldRules('title')
            ->minLength(4)->setCustomError('Введите краткое описание')->chainRule()
            ->maxLength(64)->setCustomError('Краткое описание должно быть кратким. Распишите лучше в поле "Детали"');
        $validator->fieldRules('city_from')
            ->required()->setCustomError('Укажите город отправки')->chainRule()
            ->maxLength(64)->setCustomError('Названиегорода слишком длинное. Вы и правда там живете?');
        $validator->fieldRules('city_to')
            ->required()->setCustomError('Укажите город доставки')->chainRule()
            ->maxLength(64)->setCustomError('Название города доставки слишком длинное. Такой и правда есть?');
        $validator->fieldRules('address_from')
            ->required()->setCustomError('Укажите адрес отправки');
        $validator->fieldRules('address_to')
            ->required()->setCustomError('Укажите адрес доставки');
        $validator->fieldRules('client[client_name]')
            ->required()->setCustomError('Введите ваше имя');
        $validator->fieldRules('client[client_email]')->breakOnFirst()
            ->required()->setCustomError('Введите e-mail')->chainRule()
            ->email()->setCustomError('Введите корректный e-mail, на него будут отправляться предложения');
        $validator->fieldRules('client[client_phone]')->breakOnFirst()
            ->required()->setCustomError('Введите номер телефона')->chainRule()
            ->pregMatch('/\+[0-9]{2}\s\([0-9]{3}\)\s[0-9]{3}\-[0-9]{2}\-[0-9]{2}/i')
            ->setCustomError('Введите корректный номер телефона');
        $validator->fieldRules('weight')
            ->float()->setCustomError('В поле Вес должны быть только цифры');
        $validator->fieldRules('max_price')
            ->float()->setCustomError('В поле Максимальная цена должны быть только цифры');
        $validator->fieldRules('date_move')
            ->dateTime()->setCustomError('Укажите корректную дату отправки');
        return $validator;
    }
}
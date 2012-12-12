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
    }

    public function addorderAction()
    {
//        if (!$this->session()->get('user_id', false)) {
//            $this->redirect($this->url('login'));
//        }
        $this->_view->setTitle('Заказать перевозку');

        $orderForm = \Cargo\Blocks\Order\OrderForm::forge()
            ->setConfigs('order-form', $this->url('addorder'))
            ->setHorizontal()
            ->setValidator($this->_getOrderValidator())->loadFromRequest($this->request());

        $cities = \Magelight\Geo\Models\City::orm()
            ->selectFields(['city_name_ru'])
            ->whereEq('country_id', 2)
            ->fetchColumn(false);
        $orderForm->set('cities', json_encode($cities));
        $userData = \Magelight\Auth\Models\User::find($this->session()->get('user_id'));

        $categories = \Cargo\Models\Category::orm()->fetchAll();
        $orderForm->set('categories', $categories);
        if (!$orderForm->isEmptyRequest() && $orderForm->validate()) {
            $data = $orderForm->getFieldValue('order');
            $data['user_id'] = $this->session()->get('user_id');
            $order = \Cargo\Models\Order::forge($data, true);
            if ($order->save()) {
                $orderGeo = \Cargo\Models\OrderGeo::forge([], true)->createForOrder($order->id);
                $orderGeo->save();
            }
        } else {

            if ($userData) {
                $client = [
                    'name' => $userData->name,
                    'email' => $userData->email,
                    'phone' => \Magelight\Auth\Models\Contact::orm()
                        ->selectFields(['content'])
                        ->whereEq('type', 'phone')
                        ->whereEq('user_id', $userData->id)
                        ->fetchFirstColumnElement()
                ];
                $orderForm->setFieldValue('client', $client);
            }
        }
        $this->_view->sectionReplace('content', $orderForm);
        $this->renderView();
    }

    protected function _getOrderValidator()
    {
        $validator = \Magelight\Webform\Models\Validator::forge()->setErrorsLimit(2);
        $validator->fieldRules('order[category]')
            ->required()->setCustomError('Укажите категорию');

        $validator->fieldRules('order[city_from]')
            ->required()->setCustomError('Укажите город отправки')->chainRule()
            ->maxLength(64)->setCustomError('Названиегорода слишком длинное. Вы и правда там живете?');
        $validator->fieldRules('order[city_to]')
            ->required()->setCustomError('Укажите город доставки')->chainRule()
            ->maxLength(64)->setCustomError('Название города доставки слишком длинное. Такой и правда есть?');
        $validator->fieldRules('order[address_from]')
            ->required()->setCustomError('Укажите адрес отправки');
        $validator->fieldRules('order[address_to]')
            ->required()->setCustomError('Укажите адрес доставки');
        $validator->fieldRules('client[name]')
            ->required()->setCustomError('Введите ваше имя');
        $validator->fieldRules('client[email]')->breakOnFirst()
            ->required()->setCustomError('Введите e-mail')->chainRule()
            ->email()->setCustomError('Введите корректный e-mail, на него будут отправляться предложения');
        $validator->fieldRules('client[phone]')->breakOnFirst()
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

    public function listAction()
    {
        $this->_view->setTitle('Список заказов');
        $list = \Cargo\Blocks\Order\OrderList::forge();
        $currentPage = $this->request()->getGet('page', 0);
        $collection = \Magelight\Dbal\Db\Collection::forge(
            \Cargo\Models\Order::orm()->selectFields(['u.name', 'u.photo AS user_photo', 'orders.*', 'g.*'])
                ->joinLeft('users', 'u', 'u.id = orders.user_id')
                ->joinLeft('order_geo', 'g', 'g.order_id = orders.id')
                ->orderByDesc('orders.date_added')
        )->setLimit(15)->setPage($currentPage)->useCache(['orders_list_no_filter', 15, $currentPage], 50);

        $list->set('orders', $collection->fetchAll(true));
        $pager = \Magelight\Core\Blocks\Pager::forge($collection)->setRoute($this->_routeAction['match'])->addClass('pagination-small');
        $this->_view->sectionReplace('orders-pager', $pager);
        $this->_view->sectionReplace('content', $list);
        $this->renderView();
    }
}
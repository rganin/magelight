<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 09.12.12
 * Time: 23:31
 * To change this template use File | Settings | File Templates.
 */

namespace Cargo\Blocks\Order;

class OrderForm extends \Magelight\Webform\Blocks\Form
{
    protected $_template = 'Cargo/templates/order/order-form.phtml';

    public function init()
    {
        \Magelight\Core\Blocks\Document::getFromRegistry()->addJs(
            'modules/private/Magelight/Webform/static/js/jquery.maskedinput.js',
            'modules/private/Magelight/Core/static/js/jquery.js'
        );
        \Magelight\Core\Blocks\Document::getFromRegistry()->addJs(
            'modules/private/Magelight/Webform/static/js/bootstrap-datepicker.js',
            'modules/private/Magelight/Core/static/js/jquery.js'
        );
        \Magelight\Core\Blocks\Document::getFromRegistry()->addCss(
            'modules/private/Magelight/Webform/static/css/datepicker.css',
            'modules/private/Magelight/Core/static/css/bootstrap.min.css'
        );
    }
}
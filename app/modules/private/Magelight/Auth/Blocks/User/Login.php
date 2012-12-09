<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 09.12.12
 * Time: 21:20
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Auth\Blocks\User;

class Login extends \Magelight\Block
{
    protected $_template = 'Magelight/Auth/templates/user/login.phtml';

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
}
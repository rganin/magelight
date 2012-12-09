<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 09.12.12
 * Time: 20:04
 * To change this template use File | Settings | File Templates.
 */

namespace Cargo\Controllers;

class Index extends \Magelight\Controller
{
    /**
     * Before execute handler
     */
    public function beforeExecute()
    {
        $this->_view = \Magelight\Core\Blocks\Document::forge();
        $this->_view->sectionAppend('body', \Cargo\Blocks\Body::forge());
        $this->_view->sectionAppend('content', \Cargo\Blocks\Home::forge());
        $this->_view->sectionAppend('login-menu-option', \Magelight\Auth\Blocks\User\LoginTopMenu::forge());
    }

    public function indexAction()
    {
        $this->renderView();
    }
}
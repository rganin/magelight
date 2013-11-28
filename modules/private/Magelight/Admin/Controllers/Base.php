<?php

namespace Magelight\Admin\Controllers;

class Base extends \Magelight\Controller
{
    public function beforeExecute()
    {
        if (!\Magelight\Admin\Helpers\Admin::getInstance()->isCurrentUserAdmin()) {
            $this->redirectInternal('no_rights');
            $this->_app->shutdown();
        }
        $this->_view = \Magelight\Core\Blocks\Document::forge()->loadPerspective('global/perspectives/admin');
        $this->_view->setGlobal('user_id', $this->session()->get('user_id'));
    }
}
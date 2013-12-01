<?php

namespace Magelight\Admin\Controllers;

class Base extends \Magelight\Controller
{
    /**
     * @var \Magelight\Core\Blocks\Breadcrumbs
     */
    protected $_breadcrumbsBlock;

    public function beforeExecute()
    {
        $this->_breadcrumbsBlock = \Magelight\Core\Blocks\Breadcrumbs::forge();
        $this->_breadcrumbsBlock->addBreadcrumb('Admin', 'admin/index');


        if (!\Magelight\Admin\Helpers\Admin::getInstance()->isCurrentUserAdmin()) {
            $this->redirectInternal('no_rights');
            $this->_app->shutdown();
        }
        $this->_view = \Magelight\Core\Blocks\Document::forge()->loadPerspective('global/perspectives/admin');
        $this->_view->setGlobal('user_id', $this->session()->get('user_id'));
        $this->view()->sectionAppend('breadcrumbs', $this->_breadcrumbsBlock);
    }
}
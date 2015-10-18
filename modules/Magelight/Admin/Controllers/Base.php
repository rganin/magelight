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
        $this->_breadcrumbsBlock->addBreadcrumb(__('Admin panel'), 'admin/index');


        if (!\Magelight\Admin\Helpers\Admin::getInstance()->isCurrentUserAdmin()) {
            $this->redirectInternal('no_rights');
            $this->app->shutdown();
        }
        $this->view = \Magelight\Core\Blocks\Document::getInstance()->loadPerspective('global/perspectives/admin');
        $this->view->setGlobal('user_id', $this->session()->get('user_id'));
        $this->view()->sectionAppend('breadcrumbs', $this->_breadcrumbsBlock);
        $this->view()->sectionAppend('dashboard-content', '');
    }
}
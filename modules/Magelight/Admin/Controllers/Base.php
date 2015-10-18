<?php

namespace Magelight\Admin\Controllers;

class Base extends \Magelight\Controller
{
    /**
     * @var \Magelight\Core\Blocks\Breadcrumbs
     */
    protected $breadcrumbsBlock;

    /**
     * {@inheritdoc}
     */
    public function beforeExecute()
    {
        $this->breadcrumbsBlock = \Magelight\Core\Blocks\Breadcrumbs::forge();
        $this->breadcrumbsBlock->addBreadcrumb(__('Admin panel'), 'admin/index');


        if (!\Magelight\Admin\Helpers\Admin::getInstance()->isCurrentUserAdmin()) {
            $this->redirectInternal('no_rights');
            $this->app->shutdown();
        }
        $this->view = \Magelight\Core\Blocks\Document::getInstance()->loadPerspective('global/perspectives/admin');
        $this->view->setGlobal('user_id', $this->session()->get('user_id'));
        $this->view()->sectionAppend('breadcrumbs', $this->breadcrumbsBlock);
        $this->view()->sectionAppend('dashboard-content', '');
        return parent::beforeExecute();
    }
}
<?php

namespace Magelight\Config\Controllers;


use Magelight\Config;

class Index extends \Magelight\Admin\Controllers\Base
{
    public function beforeExecute()
    {
        parent::beforeExecute();
        $this->breadcrumbsBlock->addBreadcrumb(__('Config'), 'admin/config');
        return $this;
    }

    public function indexAction()
    {
        $this->view()->sectionReplace('content', Config\Blocks\Config\Container::forge());
        $this->renderView();
    }






}

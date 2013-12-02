<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 01.12.13
 * Time: 20:18
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Visitors\Controllers;

class Visitors extends \Magelight\Admin\Controllers\Base
{
    public function indexAction()
    {
        $this->_breadcrumbsBlock->addBreadcrumb('Visitors', 'admin/visitors');
        $this->view()->sectionReplace('content', 'Visitors');
        $this->renderView();
    }
}
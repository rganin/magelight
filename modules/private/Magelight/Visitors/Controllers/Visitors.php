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
        $collection = \Magelight\Db\Collection::forge(
            \Magelight\Visitors\Models\Visitor::orm()
        )->setLimit(30);
        $page = $this->request()->getRequest('page', 0);
        $this->view()->sectionReplace('content', \Magelight\Visitors\Blocks\VisitorsList::forge($collection, $page));
        $this->renderView();
    }
}
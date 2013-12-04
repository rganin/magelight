<?php
/**
 * Magelight
 *
 * NOTICE OF LICENSE
 *
 * This file is open source and it`s distribution is based on
 * Open Software License (OSL 3.0). You can obtain license text at
 * http://opensource.org/licenses/osl-3.0.php
 *
 * For any non license implied issues please contact rganin@gmail.com
 *
 * DISCLAIMER
 *
 * This file is a part of a framework. Please, do not modify it unless you discard
 * further updates.
 *
 * @version 1.0
 * @author Roman Ganin
 * @copyright Copyright (c) 2013 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight\Visitors\Controllers;

/**
 * Class Visitors
 *
 * @package Magelight\Visitors\Controllers
 */
class Visitors extends \Magelight\Admin\Controllers\Base
{
    /**
     * Visitors controller index action
     */
    public function indexAction()
    {
        $this->_breadcrumbsBlock->addBreadcrumb('Visitors', 'admin/visitors');
        $collection = \Magelight\Db\Collection::forge(
            \Magelight\Visitors\Models\Visitor::orm()->orderByDesc('time')
        )->setLimit(30);
        $page = $this->request()->getRequest('page', 0);
        $this->view()->sectionReplace('content', \Magelight\Visitors\Blocks\VisitorsList::forge($collection, $page));
        $this->renderView();
    }
}

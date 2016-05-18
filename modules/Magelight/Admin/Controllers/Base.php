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
 * @copyright Copyright (c) 2012-2015 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight\Admin\Controllers;

/**
 * Class Base
 * @package Magelight\Admin\Controllers
 */
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
            $this->redirect($this->url('no_rights'));
            $this->app->shutdown();
            die();
        }
        $this->view = \Magelight\Core\Blocks\Document::getInstance()->loadLayout('global/layouts/admin');
        $this->view->setGlobal('user_id', $this->session()->get('user_id'));
        $this->view()->sectionAppend('breadcrumbs', $this->breadcrumbsBlock);
        $this->view()->sectionAppend('dashboard-content', '');
        return parent::beforeExecute();
    }
}

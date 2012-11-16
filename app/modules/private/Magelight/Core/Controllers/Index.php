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
 * @copyright Copyright (c) 2012 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight\Core\Controllers;

class Index extends \Magelight\Controller
{

    /**
     * Before execute handler
     */
    public function beforeExecute()
    {
        $this->_view = \Magelight\Core\Blocks\Document::forge();
        $this->_view->sectionAppend('body', \Magelight\Core\Blocks\Body::forge());
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $db = $this->app()->db();
        $orm = new \Magelight\Dbal\Db\Mysql\Orm('users', 'id', 'Users', $db);
//        var_dump($orm->selectFields(['DISTINCT name', 'password AS pass', 'email'])->whereEq('name', 'Admin')->fetchAll());
        $this->_view->set('title', 'Welcome');
        $this->_view->sectionAppend('content', \Magelight\Core\Blocks\Content::forge());
        \Magelight\Core\Blocks\Document::getFromRegistry()->addMeta(['name' => 'description', 'content' => '123']);
         $this->renderView();
    }

    /**
     * No route action
     */
    public function no_routeAction()
    {
        $this->_view->set('title', 'Page not found');

        $block = \Magelight\Core\Blocks\Error::forge();
        /* @var $block \Magelight\Core\Blocks\Error */

        $block->setTemplate(\Magelight\Core\Blocks\Error::TEMPLATE_404);
        $this->_view->sectionReplace('content', $block);
        $this->app()->log('404 - not found ' . $this->request()->getRequestRoute());
        $this->renderView();
    }

    public function loginAction()
    {
        $form = \Magelight\Core\Blocks\Form\Form::forge();
        /* @var $form \Magelight\Core\Blocks\Form\Form */

        $this->_view->set('title', 'Log in');
        $block = \Magelight\Core\Blocks\Login::forge();
        $this->_view->sectionReplace('content', $block);
        $this->renderView();
    }

    public function registerAction()
    {
        $form = \Magelight\Core\Blocks\Form\Form::forge();
        /* @var $form \Magelight\Core\Blocks\Form\Form */

        $this->_view->set('title', 'Register new user');
        $block = \Magelight\Core\Blocks\Register::forge();
        $this->_view->sectionReplace('content', $block);
        $this->renderView();
    }
}

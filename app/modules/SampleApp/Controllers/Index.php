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

namespace SampleApp\Controllers;

/**
 * Index app controller
 */
class Index extends \Magelight\Controller
{
    /**
     * Before execute handler
     *
     * @return \Magelight\Controller|void
     */
    public function beforeExecute()
    {
        $this->view = \Magelight\Core\Blocks\Document::getInstance();
        $this->view->sectionAppend('body', \SampleApp\Blocks\Body::forge());
        return parent::beforeExecute();
    }
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->view->set('title', 'Welcome');
        $this->view->sectionAppend('content', \SampleApp\Blocks\Welcome::forge());
        \Magelight\Core\Blocks\Document::getInstance()->addMeta(['name' => 'description', 'content' => '123']);
        $this->renderView();
    }

    /**
     * No route action
     */
    public function no_routeAction()
    {
        $this->view->set('title', 'Page not found');

        /* @var $block \SampleApp\Blocks\Error */
        $block = \SampleApp\Blocks\Error::forge();

        $block->setTemplate(\SampleApp\Blocks\Error::TEMPLATE_404);
        $this->view->sectionReplace('content', $block);
        \Magelight\Log::getInstance()->add('404 - not found ' . $this->request()->getRequestRoute());
        $this->renderView();
    }
}

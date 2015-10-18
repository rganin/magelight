<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category
 * @package
 * @subpackage
 * @author
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
        return $this;
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

<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 09.12.12
 * Time: 20:04
 * To change this template use File | Settings | File Templates.
 */

namespace Cargo\Controllers;

/**
 * @property  \Magelight\Core\Blocks\Document $_view
 */
class Index extends \Magelight\Controller
{
    /**
     * Before execute handler
     */
    public function beforeExecute()
    {
        $this->_view = \Magelight\Core\Blocks\Document::forge();
        $this->_view->sectionAppend('body', \Cargo\Blocks\Body::forge());
    }

    public function indexAction()
    {
        $this->_view->sectionAppend('content', \Cargo\Blocks\Home::forge()->useCache('main_page'));
        $this->renderView();
    }
}
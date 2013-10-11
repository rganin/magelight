<?php
/**
 * Created by JetBrains PhpStorm.
 * User: rganin
 * Date: 11.10.13
 * Time: 15:52
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Core\Controllers;

class BaseController extends \Magelight\Controller
{
    public function beforeExecute()
    {
        $this->_view = \Magelight\Core\Blocks\Document::forge();
        $this->_view->sectionAppend('body', \Magelight\Core\Blocks\Body::forge());
    }
}
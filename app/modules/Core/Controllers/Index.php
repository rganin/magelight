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
 * @version $$version_placeholder_notice$$
 * @author $$author_placeholder_notice$$
 * @copyright Copyright (c) 2012 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Core\Controllers;

class Index extends \Magelight\Controller
{
    public function beforeExecute()
    {
        $this->_view = \Core\Blocks\Document::forge();
        $this->_view->sectionAppend('body', \Core\Blocks\Main::forge());
    }

    public function test($a)
    {
        return $a;
    }
    
    public function indexAction()
    {
        $this->_view->title = 'Welcome';
        \Core\Blocks\Document::getFromRegistry()->addMeta(array('name' => 'description', 'content' => '123'));
         $this->renderView();
    }
}

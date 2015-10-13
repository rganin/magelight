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

namespace Magelight\Core\Controllers;

/**
 * Class BaseController
 *
 * @package Magelight\Core\Controllers
 */
class BaseController extends \Magelight\Controller
{
    /**
     * Before execute handler
     *
     * @return \Magelight\Controller|void
     */
    public function beforeExecute()
    {
        $this->_view = \Magelight\Core\Blocks\Document::getInstance();
        $this->_view->sectionAppend('body', \Magelight\Core\Blocks\Body::forge());
        return $this;
    }
}
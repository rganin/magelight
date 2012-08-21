<?php
/**
 * $$name_placeholder_notice$$
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
 * @version   $$version_placeholder_notice$$
 * @uthor     $$author_placeholder_notice$$
 * @copyright Copyright (c) 2012 rganin (rganin@gmail.com)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Core\Controllers;

class Errors extends \Bike\Controller
{
    public function notFoundAction()
    {
        $this->view()->head()->setTitle('Not found');
        $this->response()->addHeader('HTTP/1.0 404 Not Found');
        $this->view()->replaceSection('Core\\Blocks\\Errors\\NotFound', 'body');
        $this->renderView();
    }
}
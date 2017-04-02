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

namespace Magelight\Visitors\Hooks;

use Magelight\Hook\AbstractHook;
use Magelight\Visitors\Models\Visitor;

/**
 * Class EncountVisitor hook
 *
 * @package Magelight\Visitors\Hooks
 */
class EncountVisitor extends AbstractHook
{
    public function afterAfterExecute()
    {
        if (php_sapi_name() == "cli") {
            return $this;
        }
        /** @var \Magelight\Http\Request $request */
        $request = \Magelight\Http\Request::getInstance();
        if ($request instanceof \Magelight\Http\Request) {
            $requestRoute = $request->getRequestRoute();
            Visitor::forge()->encount($requestRoute);
        }
        return $this;
    }
}

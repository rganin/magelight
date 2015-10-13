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
namespace Magelight\Visitors\Models;

/**
 * Class Observer
 * @package Magelight\Visitors\Models
 */
class Observer extends \Magelight\Observer
{
    /**
     * Execute observer
     *
     * @return Observer
     */
    public function execute()
    {
        /** @var $request \Magelight\Http\Request */
        $request = $this->_arguments['request'];
        if ($request instanceof \Magelight\Http\Request) {
            $requestRoute = $request->getRequestRoute();
            Visitor::forge()->encount($requestRoute);
        }
        return $this;
    }
}
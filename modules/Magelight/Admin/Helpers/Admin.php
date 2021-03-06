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

namespace Magelight\Admin\Helpers;

/**
 * Class Admin
 *
 * @package Magelight\Admin\Helpers
 */
class Admin
{
    use \Magelight\Traits\TForgery;

    /**
     * Check is current user admin user
     *
     * @return bool
     */
    public function isCurrentUserAdmin()
    {
        $userId = \Magelight\Http\Session::getInstance()->get('user_id');
        $model = \Magelight\Admin\Models\AdminUser::findBy('user_id', $userId);
        return !empty($model);
    }
}

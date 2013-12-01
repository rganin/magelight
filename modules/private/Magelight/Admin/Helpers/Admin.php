<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 27.11.13
 * Time: 22:47
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Admin\Helpers;

class Admin
{
    use \Magelight\Traits\TForgery;

    public function isCurrentUserAdmin()
    {
        $userId = \Magelight::app()->session()->get('user_id');
        $model = \Magelight\Admin\Models\AdminUser::findBy('user_id', $userId);
        return !empty($model);
    }
}
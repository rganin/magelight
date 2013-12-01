<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 09.12.12
 * Time: 20:07
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Admin\Blocks;

class Body extends \Magelight\Block
{
    protected $_template = 'Magelight/Admin/templates/body.phtml';

    public function init()
    {
        $currentUserId = \Magelight::app()->session()->get('user_id');
        if (!empty($currentUserId)) {
            if ($user = \Magelight\Auth\Models\User::find($currentUserId)) {
                $userData = $user->asArray();
                $this->setGlobal('user_data', $userData);
                $this->setGlobal('is_current_user_transporter', $user::isTransporter($currentUserId));
            }
        }
        $document = \Magelight\Core\Blocks\Document::getFromRegistry();
        $document->addMeta([
            'http-equiv'=> "content-type",
            'content' => "text/html; charset=utf-8",
        ]);
        $document->addCss('modules/private/Magelight/Core/static/css/bootstrap.css');
        $document->addCss('modules/private/Magelight/Core/static/css/hint.css');
        $document->addJs('modules/private/Magelight/Core/static/js/jquery.js');
        $document->addJs('modules/private/Magelight/Core/static/js/bootstrap.min.js');
    }
}
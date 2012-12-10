<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 09.12.12
 * Time: 20:07
 * To change this template use File | Settings | File Templates.
 */

namespace Cargo\Blocks;

class Body extends \Magelight\Core\Blocks\Body
{
    protected $_template = 'Cargo/templates/body.phtml';

    public function init()
    {
        $this->sectionAppend('top', Top::forge());
        $currentUserId = \Magelight::app()->session()->get('user_id');
        if (!empty($currentUserId)) {
            if ($user = \Magelight\Auth\Models\User::find($currentUserId)) {
                $this->setGlobal('user_data', $user->asArray());
            }
        }
        $document = \Magelight\Core\Blocks\Document::getFromRegistry();
        $document->addMeta([
            'http-equiv'=> "content-type",
            'content' => "text/html; charset=utf-8",
        ]);
        $document->addCss('modules/private/Magelight/Core/static/css/bootstrap.min.css');
        $document->addCss('modules/public/Cargo/static/css/cargo.css');
        $document->addJs('modules/private/Magelight/Core/static/js/jquery.js');
        $document->addJs('modules/private/Magelight/Core/static/js/bootstrap.min.js');
    }
}
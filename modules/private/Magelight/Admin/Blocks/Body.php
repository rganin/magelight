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

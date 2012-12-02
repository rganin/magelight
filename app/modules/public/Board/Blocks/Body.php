<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 01.12.12
 * Time: 18:53
 * To change this template use File | Settings | File Templates.
 */

namespace Board\Blocks;

class Body extends \Magelight\Block
{
    protected $_template = 'Board/templates/body.phtml';

    public function init()
    {
        $this->sectionAppend('top', Top::forge());

        $document = \Magelight\Core\Blocks\Document::getFromRegistry();
        $document->addMeta([
            'http-equiv'=> "content-type",
            'content' => "text/html; charset=utf-8",
        ]);
        $document->addCss('modules/private/Magelight/Core/static/css/bootstrap.min.css');
        $document->addCss('modules/public/Board/static/css/board.css');
        $document->addJs('modules/private/Magelight/Core/static/js/jquery.js');
        $document->addJs('modules/private/Magelight/Core/static/js/bootstrap.min.js');
    }
}
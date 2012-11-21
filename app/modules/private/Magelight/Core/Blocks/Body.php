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
 * @copyright Copyright (c) 2012 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight\Core\Blocks;

class Body extends \Magelight\Block
{
    protected $_template = 'Magelight/Core/templates/body.phtml';

    public function init()
    {
        $this->sectionAppend('top', \Magelight\Core\Blocks\Top::forge());
        $document = \Magelight\Core\Blocks\Document::getFromRegistry();
        $document->addMeta(array(
            'name' => 'keywords',
            'content' => 'welcome app, magelight'
        ));
        $document->addCss('modules/private/Magelight/Core/static/css/bootstrap.min.css');
        $document->addCss('modules/private/Magelight/Core/static/css/core.css');
        $document->addJs('modules/private/Magelight/Core/static/js/jquery.js');
        $document->addJs('modules/private/Magelight/Core/static/js/bootstrap.min.js');
    }
}
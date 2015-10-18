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

namespace SampleApp\Blocks;

class Body extends \Magelight\Block
{
    /**
     * @var string
     */
    protected $template = 'SampleApp/templates/body.phtml';

    /**
     * {@inheritdoc}
     */
    public function initBlock()
    {
        $this->sectionAppend('top', Top::forge());
        $document = \Magelight\Core\Blocks\Document::getInstance();
        $document->addMeta([
            'http-equiv' => "content-type",
            'content' => "text/html; charset=utf-8",
        ]);
        $document->addMeta([
            'name' => 'keywords',
            'content' => 'welcome app, magelight'
        ]);
        $document->addCss('Magelight/Core/static/css/bootstrap.min.css');
        $document->addCss('Magelight/Core/static/css/core.css');
        $document->addJs('Magelight/Core/static/js/jquery.js');
        $document->addJs('Magelight/Core/static/js/bootstrap.min.js');
        return parent::initBlock();
    }
}

<?php

namespace Magelight\Config\Blocks\Config;

use Magelight\Block;

class Container extends Block
{
    protected $template = 'Magelight/Config/templates/config/container.phtml';

    public function initBlock()
    {
        $this->sectionReplace('config-tree', Tree::forge());
        return parent::initBlock();
    }
}

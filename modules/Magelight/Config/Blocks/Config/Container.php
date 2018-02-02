<?php

namespace Magelight\Config\Blocks\Config;

use Magelight\Block;
use Magelight\Http\Request;

class Container extends Block
{
    protected $template = 'Magelight/Config/templates/config/container.phtml';

    public function initBlock()
    {
        $this->sectionReplace('config-tree', Tree::forge());
        $this->sectionReplace('config-form', Form::forge(Request::getInstance()->getGet('path')));
        return parent::initBlock();
    }
}

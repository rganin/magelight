<?php

namespace Magelight\Core\Blocks\Grid\Cell;

use Magelight\Core\Blocks\Grid\Cell;

class Plaintext extends Cell
{
    /**
     * {@inheritdoc}
     */
    public function toHtml()
    {
        // this renderer expects that cell contains only one array element in data that is stored by column key
        $this->setContent((string)$this->getFirstDataArrayElement());
        return parent::toHtml();
    }
}

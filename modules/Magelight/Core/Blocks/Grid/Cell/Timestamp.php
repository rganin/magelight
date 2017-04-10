<?php

namespace Magelight\Core\Blocks\Grid\Cell;

use Magelight\Core\Blocks\Grid\Cell;

class Timestamp extends Cell
{
    /**
     * {@inheritdoc}
     */
    public function toHtml()
    {
        return $this->dateTime($this->getFirstDataArrayElement());
    }
}

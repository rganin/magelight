<?php

namespace Magelight\Grid\Blocks\Grid\Column\Filter;

use Magelight\Db\Common\Expression\Expression;

class Text extends AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    public function __forge()
    {
        $this->setTag('input')->setAttribute('type', 'test')->setClass('form-control input-sm');
    }

    /**
     * {@inheritdoc}
     */
    public function getFilterSqlExpression()
    {
        return Expression::forge("{$this->getName()} LIKE ?", "%{$this->getValue()}%");
    }
}
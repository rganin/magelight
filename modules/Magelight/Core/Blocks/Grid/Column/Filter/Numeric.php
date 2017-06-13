<?php

namespace Magelight\Core\Blocks\Grid\Column\Filter;

use Magelight\Db\Common\Expression\Expression;

class Numeric extends AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    public function __forge()
    {
        $this->setTag('input')->setAttribute('type', 'number')->setClass('form-control input-sm');
    }

    /**
     * {@inheritdoc}
     */
    public function getFilterSqlExpression()
    {
        return Expression::forge("{$this->getName()} = ?", $this->getValue());
    }
}
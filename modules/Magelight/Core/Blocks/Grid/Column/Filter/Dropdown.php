<?php

namespace Magelight\Core\Blocks\Grid\Column\Filter;

use Magelight\Core\Blocks\Grid\Column;
use Magelight\Db\Common\Expression\Expression;
use Magelight\Webform\Blocks\Elements\Select;

class Dropdown extends Select implements FilterInterface
{

    /**
     * @var Column
     */
    protected $column;

    /**
     * {@inheritdoc}
     */
    public function __forge()
    {
        $this->setClass('form-control input-sm');
    }

    /**
     * {@inheritdoc}
     */
    public function getFilterSqlExpression()
    {
        return Expression::forge("{$this->getName()} = ?", $this->getValue());
    }

    /**
     * Attach column
     *
     * @param Column $column
     * @return $this
     */
    public function setColumn(Column $column)
    {
        $this->column = $column;
        $this->setName(implode($column->getFields()));
        return $this;
    }

    /**
     * Set field to be used in filter.
     *
     * @param string $rowFieldName
     *
     * @return $this
     */
    public function setFilterField($rowFieldName)
    {
        $this->setName($rowFieldName);
        return $this;
    }
}
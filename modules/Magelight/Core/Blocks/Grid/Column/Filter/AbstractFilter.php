<?php

namespace Magelight\Core\Blocks\Grid\Column\Filter;

use Magelight\Core\Blocks\Grid\Column;
use Magelight\Db\Common\Expression\Expression;
use Magelight\Webform\Blocks\Elements\Abstraction\Field;

/**
 * Class AbstractFilter
 * @package Magelight\Core\Blocks\Grid\Filter
 *
 * @method static $this forge()
 */
abstract class AbstractFilter extends Field implements FilterInterface
{
    /**
     * @var Column
     */
    protected $column;

    /**
     * Get filter SQL expression
     *
     * @return Expression
     */
    abstract public function getFilterSqlExpression();

    /**
     * Attach column
     *
     * @param Column $column
     * @return $this
     */
    public function setColumn(Column $column)
    {
        $this->column = $column;
        return $this;
    }

    /**
     * Set name of the field that collection collection should be filtered by.
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

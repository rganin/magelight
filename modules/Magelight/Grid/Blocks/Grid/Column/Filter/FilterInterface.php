<?php

namespace Magelight\Grid\Blocks\Grid\Column\Filter;

use Magelight\Grid\Blocks\Grid\Column;
use Magelight\Db\Common\Expression\Expression;
use Magelight\Webform\Blocks\Form;

interface FilterInterface
{
    /**
     * Get filter SQL expression
     *
     * @return Expression
     */
    public function getFilterSqlExpression();

    /**
     * Attach column
     *
     * @param Column $column
     * @return $this
     */
    public function setColumn(Column $column);

    /**
     * Set field to be used in filter.
     *
     * @param string $rowFieldName
     *
     * @return $this
     */
    public function setFilterField($rowFieldName);

    /**
     * @return mixed
     */
    public function toHtml();

    /**
     * @param Form $form
     * @return mixed
     */
    public function setForm(Form $form);

    /**
     * Is filter empty
     *
     * @return bool
     */
    public function isEmptyValue();
}

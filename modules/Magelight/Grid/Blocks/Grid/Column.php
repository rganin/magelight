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
 * @copyright Copyright (c) 2012-2015 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight\Grid\Blocks\Grid;

use Magelight\Core\Blocks\Element;
use Magelight\Grid\Blocks\Grid;
use Magelight\Traits\TForgery;

/**
 * Class Column
 * @package Magelight\Grid\Blocks\Grid
 *
 * @method static $this forge()
 */
class Column
{
    use TForgery;

    /**
     * @var Grid\Column\Filter\AbstractFilter
     */
    protected $filter;

    /**
     * @var Grid
     */
    protected $grid;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var bool|int
     */
    protected $visible = true;

    /**
     * @var bool
     */
    protected $sortable = false;

    /**
     * Field to be passed to renderer
     *
     * @var string[]
     */
    protected $fields;

    /**
     * @var Cell
     */
    protected $cellRenderer;

    /**
     * @var string
     */
    protected $sortField;

    /**
     * Forgery constructor.
     */
    public function __forge()
    {

    }

    /**
     * Set column grid
     *
     * @param Grid $grid
     * @return $this
     */
    public function setGrid(Grid $grid)
    {
        $this->grid = $grid;
        return $this;
    }

    /**
     * Set column title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Set cell field
     *
     * @param string $rowFieldName - index of field in row assoc array
     * @param string|null $fieldNameAlias - alias of the field for cell renderer
     * @return Column
     */
    public function setCellField($rowFieldName, $fieldNameAlias = null)
    {
        return $this->setCellFields([$fieldNameAlias ?: $rowFieldName => $rowFieldName]);
    }

    /**
     * Set sort fields to be added to ORDER BY statement of the collection
     *
     * @param string $rowFieldName
     *
     * @return $this
     */
    public function setSortField($rowFieldName)
    {
        $this->sortField = $rowFieldName;
        return $this;
    }

    /**
     * Get fields to be used for sorting
     *
     * @return string
     */
    public function getSortField()
    {
        return $this->sortField;
    }

    /**
     * Set column fields for cell
     *
     * @param array $rowFieldNamesArray - ['alias' => 'field', 'field2', 'alias_2' => 'field_3']
     * @return $this
     */
    public function setCellFields(array $rowFieldNamesArray)
    {
        $this->fields = $rowFieldNamesArray;
        if (!$this->getSortField()) {
            $this->setSortField(array_values($rowFieldNamesArray)[0]);
        }
        return $this;
    }

    /**
     * Set is column visible
     * todo: implement visibility setting and processing
     * @param bool $flag
     * @return $this
     */
    public function setVisible($flag = true)
    {
        $this->visible = (bool)$flag;
        return $this;
    }

    /**
     * Set is column sortable
     * todo: implement sortability setting and processing
     * @param bool $flag
     * @return $this
     */
    public function setSortable($flag = true)
    {
        $this->sortable = (bool)$flag;
        return $this;
    }

    /**
     * Is column sortable
     *
     * @return bool
     */
    public function isSortable()
    {
        return $this->sortable && !empty($this->sortField);
    }

    /**
     * Get column title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get column fields
     *
     * @return \string[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Get cache key
     *
     * @return string
     */
    public function getKey()
    {
        return implode(',', $this->fields) . $this->getTitle();
    }

    /**
     * Set renderer for cell
     *
     * @param Cell|null $renderer
     *
     * @return $this
     */
    public function setCellRenderer(Cell $renderer = null)
    {
        $this->cellRenderer = $renderer;
        $this->cellRenderer->useFields($this->getFields());
        return $this;
    }

    /**
     * Get cell renderer
     *
     * @return Cell|Element
     */
    public function getCellRenderer()
    {
        if (!$this->cellRenderer) {
            return Grid\Cell\Plaintext::forge();
        }
        return $this->cellRenderer;
    }

    /**
     * Set filter for column
     *
     * @param Column\Filter\FilterInterface $filter
     * @return $this
     */
    public function setFilter(Grid\Column\Filter\FilterInterface $filter)
    {
        $this->filter = $filter;
        if (!$filter->getName()) {
            $this->filter->setFilterField(array_values($this->getFields())[0]);
        }
        $this->filter->setColumn($this);
        return $this;
    }

    /**
     * Get filter field instance
     *
     * @return Column\Filter\FilterInterface
     */
    public function getFilter()
    {
        return $this->filter;
    }
}

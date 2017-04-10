<?php

namespace Magelight\Core\Blocks;

use Magelight\Block;
use Magelight\Core\Blocks\Grid\Column;
use Magelight\Core\Blocks\Grid\Row;
use Magelight\Db\Collection;

/**
 * Class Grid
 * @package Magelight\Core\Blocks
 *
 * @property string $class
 *
 * @method static $this forge()
 */
class Grid extends Block
{
    protected $template = 'Magelight/Core/templates/grid.phtml';

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var Pager
     */
    protected $pager;

    /**
     * @var Column[]
     */
    protected $columns = [];

    /**
     * @var Row[]
     */
    protected $rows = [];

    /**
     * Forgery constructor
     */
    public function __forge()
    {
        $this->pager = Pager::forge();
    }

    /**
     * @param Collection $collection
     * @return $this
     */
    public function setDataSource(Collection $collection)
    {
        $this->collection = $collection;
        $this->pager->setCollection($collection);
        return $this;
    }

    /**
     * @param int $page
     * @return $this
     */
    public function setCurrentPage($page)
    {
        $this->collection->setPage($page);
        $this->pager->setCurrentPage($page);
        return $this;
    }

    public function setPerPage($perPage)
    {
        $this->pager->setPerPage($perPage);
        return $this;
    }

    /**
     * Add column to grid
     *
     * @param Column $column
     *
     * @return $this
     */
    public function addColumn(Column $column)
    {
        $this->columns[$column->getKey()] = $column;
        return $this;
    }

    /**
     * Get grid columns
     *
     * @return Column[]
     */
    public function getColumns()
    {
        return $this->columns;
    }

    protected function loadData()
    {
        if (empty($this->rows)) {
            foreach ($this->collection->fetchAll() as $rowData) {
                $this->rows[] = Row::forge($rowData);
            }
        }
    }

    public function getRows()
    {
        $this->loadData();
        $this->rows;
        return $this->rows;
    }

    /**
     * @param Column $column
     * @param Row $row
     * @return string
     */
    public function renderCellContent(Column $column, Row $row)
    {
        return $column->getCellRenderer()
            ->set(
                'data',
                $row->getCellData(
                    $column->getFields()
                )
            )->toHtml();
    }
}

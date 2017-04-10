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
     * Set pager current page
     *
     * @param int $page
     * @return $this
     */
    public function setCurrentPage($page)
    {
        $this->collection->setPage($page);
        $this->pager->setCurrentPage($page);
        return $this;
    }

    /**
     * Set items count per page
     *
     * @param int $perPage
     * @return $this
     */
    public function setPerPage($perPage)
    {
        $this->pager->setPerPage($perPage);
        return $this;
    }

    /**
     * Set pager route
     *
     * @param string $match
     * @param array $routeParams
     * @return $this
     */
    public function setPagerRoute($match, $routeParams = [])
    {
        $this->pager->setRoute($match, $routeParams);
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

    /**
     * Load grid data
     *
     * @return void
     */
    protected function loadData()
    {
        if (empty($this->rows)) {
            foreach ($this->collection->fetchAll() as $rowData) {
                $this->rows[] = Row::forge($rowData);
            }
        }
    }

    /**
     * Get grid rows
     *
     * @return Row[]
     */
    public function getRows()
    {
        $this->loadData();
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

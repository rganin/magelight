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
use Magelight\Profiler;
use Magelight\Webform\Blocks\Form;

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
     * @var int
     */
    protected $profileIndex;

    /**
     * @var bool
     */
    protected $enableRenderProfiling;

    /**
     * @var Form
     */
    protected $filterForm;

    /**
     * Forgery constructor
     */
    public function __forge()
    {
        $this->pager = Pager::forge();
        $this->filterForm = Form::forge();
        Document::getInstance()->addCss('Magelight/Core/static/css/grid.css');
    }

    /**
     * Get grid filter form
     *
     * @return Form
     */
    public function getFilterForm()
    {
        return $this->filterForm;
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
        if ($column->getFilter()) {
            $column->getFilter()->setForm($this->getFilterForm());
        }
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
        $columnFields = $column->getFields() ?: [];
        return $column->getCellRenderer()
            ->resetState()
            ->set(
                'data',
                $row->getCellData(
                    $columnFields
                )
            )->toHtml();
    }

    /**
     * Enable profiling for rendering
     *
     * @param bool $flag
     * @return $this
     */
    public function setEnableRenderProfiling($flag = true)
    {
        $this->enableRenderProfiling = $flag;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toHtml()
    {
        if (!$this->enableRenderProfiling) {
            return parent::toHtml();
        }
        $this->profileIndex = Profiler::getInstance('grid-render')->startNewProfiling();
        $result = parent::toHtml();
        Profiler::getInstance('grid-render')->finish($this->profileIndex);
        return $result;
    }

    /**
     * Get result of profiling of grid render
     *
     * @return array
     */
    public function getProfilingResult()
    {
        return Profiler::getInstance('grid-render')->getProfile($this->profileIndex);
    }
}

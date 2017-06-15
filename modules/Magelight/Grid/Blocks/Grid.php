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

namespace Magelight\Grid\Blocks;

use Magelight\Block;
use Magelight\Core\Blocks\Document;
use Magelight\Core\Blocks\Pager;
use Magelight\Db\CollectionFilter;
use Magelight\Grid\Blocks\Grid\Column;
use Magelight\Grid\Blocks\Grid\Row;
use Magelight\Db\Collection;
use Magelight\Profiler;
use Magelight\Webform\Blocks\Form;

/**
 * Class Grid
 * @package Magelight\Grid\Blocks
 *
 * @property string $class
 *
 * @method static $this forge($urlMatch, $name = 'grid')
 */
class Grid extends Block
{
    protected $template = 'Magelight/Grid/templates/grid.phtml';

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
     * @var string
     */
    protected $urlMatch;

    /**
     * Forgery constructor
     * @param string $urlMatch
     * @param string $name
     */
    public function __forge($urlMatch, $name = 'grid')
    {
        $this->urlMatch = $urlMatch;

        $this->pager = Pager::forge()
            ->setRoute($urlMatch);

        $this->filterForm = Form::forge()
            ->setName($name)
            ->setAction($this->url($urlMatch))
            ->setMethod('get')
            ->loadFromRequest();

        Document::getInstance()->addCss('Magelight/Grid/static/css/grid.css');
    }

    /**
     * Set grid URL match
     *
     * @param $urlMatch
     *
     * @return $this
     */
    public function setUrlMatch($urlMatch)
    {
        $this->urlMatch = $urlMatch;
        $this->pager->setRoute($urlMatch);
        $this->filterForm->setAction($this->url($urlMatch));
        return $this;
    }

    /**
     * Get grid URL match
     *
     * @return string
     */
    public function getUrlMatch()
    {
        return $this->urlMatch;
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
    public function setCollection(Collection $collection)
    {
        $this->collection = $collection;
        $sortFields = (array)$this->filterForm->getFieldValue('sort');
        $sortDirections = [];

        foreach ($sortFields as $field => $dir) {
            // direct assignment for usability and security
            if ($dir == 'ASC') {
                $sortDirections['ASC'][] = $field;
            } elseif ($dir == 'DESC') {
                $sortDirections['DESC'][] = $field;
            }
        }
        if (!empty($sortDirections['ASC'])) {
            $this->collection->sortAscending($sortDirections['ASC']);
        }
        if (!empty($sortDirections['DESC'])) {
            $this->collection->sortDescending($sortDirections['DESC']);
        }
        foreach ($this->columns as $column) {
            if ($column->getFilter() && !$column->getFilter()->isEmptyValue()) {
                $this->collection->getDataSource()->whereEx($column->getFilter()->getFilterSqlExpression());
            }
        }
        return $this;
    }

    /**
     * Get collection bound to grid
     *
     * @return Collection
     */
    public function getCollection()
    {
        return $this->collection;
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
        $this->collection->setLimit($perPage);
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
            $column->getFilter()->setValue($this->getFilterForm()->getFieldValue($column->getFilter()->getName()));
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
            $this->pager->setCollection($this->collection);
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

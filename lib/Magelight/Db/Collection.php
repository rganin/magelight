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

namespace Magelight\Db;
use Magelight\Db\Common\Expression\ExpressionInterface;

/**
 * @method static \Magelight\Db\Collection forge(\Magelight\Db\Common\Orm $dataSourceOrm = null)
 */
class Collection
{

    use \Magelight\Traits\TForgery;
    use \Magelight\Traits\TCache;

    /**
     * Sort order
     */
    const SORT_ORDER_ASC = Common\Orm::ORDER_ASC;
    const SORT_ORDER_DESC = Common\Orm::ORDER_DESC;

    /**
     * @var Common\Orm
     */
    protected $dataSource = null;

    /**
     * Page limit
     *
     * @var int
     */
    protected $limit = 0;

    /**
     * Dataset offset
     *
     * @var int
     */
    protected $offset = 0;

    /**
     * Forgery constructor
     *
     * @param Common\Orm $dataSourceOrm
     */
    public function __forge(Common\Orm $dataSourceOrm = null)
    {
        if ($dataSourceOrm) {
            $this->setDataSource($dataSourceOrm);
        }
    }

    /**
     * Set datasource for collection
     *
     * @param Common\Orm $dataSourceOrm
     * @return Collection
     */
    public function setDataSource(Common\Orm $dataSourceOrm)
    {
        $this->dataSource = $dataSourceOrm;
        return $this;
    }

    /**
     * Get colelction data source
     *
     * @param bool $clone
     *
     * @return Common\Orm|null
     */
    public function getDataSource($clone = false)
    {
        return ($clone) ? clone $this->dataSource : $this->dataSource;
    }

    /**
     * Set limit
     *
     * @param int $limit
     * @return Collection
     */
    public function setLimit($limit = 10)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Set coolection offset
     *
     * @param int $offset
     * @return Collection
     */
    public function setOffset($offset = 0)
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Set current collection page ($offset = $page*$limit)
     *
     * @param int $page
     * @return Collection
     */
    public function setPage($page = 0)
    {
        $this->offset = $page * $this->limit;
        return $this;
    }

    /**
     * Set current limit
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Get current offset
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Fetch all data from collection by limit & offset
     *
     * @param bool $assoc
     * @param int $affectedRows
     * @return array
     */
    public function fetchAll($assoc = true, &$affectedRows = 0)
    {
        $this->proxyCacheTo($this->dataSource);
        return $this->dataSource->limit($this->limit, $this->offset)->fetchAll($assoc, $affectedRows);
    }

    /**
     * Fetch data as array of models
     *
     * @param int $affectedRows
     * @return array
     */
    public function fetchModels(&$affectedRows = 0)
    {
        $this->proxyCacheTo($this->dataSource);
        return $this->dataSource->limit($this->limit, $this->offset)->fetchModels($affectedRows);
    }

    /**
     * Get collection data total count
     *
     * @return int
     */
    public function totalCount()
    {
        return $this->dataSource->totalCount();
    }

    /**
     * Apply filter to collection
     *
     * @param CollectionFilter $filter
     *
     * @return Collection
     */
    public function applyFilter(CollectionFilter $filter)
    {
        $filterExpression = $filter->getFilterExpression();
        if ($filterExpression instanceof ExpressionInterface && !$filterExpression->isEmpty()) {
            $this->getDataSource()->whereEx($filterExpression);
            return $this;
        }

        /**
         * For backward compatibility
         * @todo remove this and migrate to new expression filter
         */
        foreach ($filter->getFilterMethods() as $method) {
            $statement = $method['statement'];
            $field = $method['field'];
            $value = $method['value'];
            $this->getDataSource()->$statement($field, $value);
        }
        return $this;
    }

    /**
     * Reset collection sorting
     *
     * @return $this
     */
    public function resetSorting()
    {
        $this->dataSource->resetOrderBy();
        return $this;
    }

    /**
     * Reset collection filters
     *
     * @return $this
     */
    public function resetFilters()
    {
        $this->dataSource->resetWhere();
        return $this;
    }

    /**
     * Sort in ascending order
     *
     * @param array $fields
     * @return $this
     */
    public function sortAscending($fields = [])
    {
        if (!is_array($fields)) {
            $fields = [$fields];
        }
        foreach ($fields as $field) {
            $this->dataSource->orderByAsc($field);
        }
        return $this;
    }

    /**
     * Sort in descending order
     *
     * @param array $fields
     * @return $this
     */
    public function sortDescending($fields = [])
    {
        if (!is_array($fields)) {
            $fields = [$fields];
        }
        foreach ($fields as $field) {
            $this->dataSource->orderByDesc($field);
        }
        return $this;
    }
}

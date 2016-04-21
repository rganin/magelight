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
 * @copyright Copyright (c) 2012 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight\Db;

/**
 * @method static \Magelight\Db\Collection forge(\Magelight\Db\Common\Orm $dataSourceOrm = null)
 */
class Collection
{

    use \Magelight\Traits\TForgery;
    use \Magelight\Traits\TCache;

    /**
     * @var Common\Orm
     */
    protected $_dataSource = null;

    /**
     * Page limit
     *
     * @var int
     */
    protected $_limit = 10;

    /**
     * Dataset offset
     *
     * @var int
     */
    protected $_offset = 0;

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
        $this->_dataSource = $dataSourceOrm;
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
        return ($clone) ? clone $this->_dataSource : $this->_dataSource;
    }

    /**
     * Set limit
     *
     * @param int $limit
     * @return Collection
     */
    public function setLimit($limit = 10)
    {
        $this->_limit = $limit;
        return $this;
    }

    /**
     * Set coolection offset
     *
     * @param int $offset
     * @return Collection
     */
    public function setOffset($offset =  0)
    {
        $this->_offset = $offset;
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
        $this->_offset = $page * $this->_limit;
        return $this;
    }

    /**
     * Set current limit
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->_limit;
    }

    /**
     * Get current offset
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->_offset;
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
        $this->proxyCacheTo($this->_dataSource);
        return $this->_dataSource->limit($this->_limit, $this->_offset)->fetchAll($assoc, $affectedRows);
    }

    /**
     * Fetch data as array of models
     *
     * @param int $affectedRows
     * @return array
     */
    public function fetchModels(&$affectedRows = 0)
    {
        $this->proxyCacheTo($this->_dataSource);
        return $this->_dataSource->limit($this->_limit, $this->_offset)->fetchModels($affectedRows);
    }

    /**
     * Get collection data total count
     *
     * @return int
     */
    public function totalCount()
    {
        return $this->_dataSource->totalCount();
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
        foreach ($filter->getFilterMethods() as $method) {
            $callMethod = $method['statement'];
            $this->getDataSource()->$callMethod($method['field'], $method['value']);
        }
        return $this;
    }
}

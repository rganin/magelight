<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 10.12.12
 * Time: 21:28
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Dbal\Db;

/**
 * @method static \Magelight\Dbal\Db\Collection forge(\Magelight\Dbal\Db\Common\Orm $dataSourceOrm)
 */
class Collection
{

    use \Magelight\Forgery;
    use \Magelight\Cache\Cache;

    /**
     * @var Mysql\Orm
     */
    protected $_dataSource = null;

    protected $_limit = 10;

    protected $_offset = 0;

    public function __forge(Common\Orm $dataSourceOrm)
    {
        $this->setDataSource($dataSourceOrm);
    }

    public function setDataSource(Common\Orm $dataSourceOrm)
    {
        $this->_dataSource = $dataSourceOrm;
        return $this;
    }

    public function getDataSource()
    {
        return $this->_dataSource;
    }

    public function setLimit($limit = 10)
    {
        $this->_limit = $limit;
        return $this;
    }

    public function setOffset($offset =  0)
    {
        $this->_offset = $offset;
        return $this;
    }

    public function setPage($page = 0)
    {
        $this->_offset = $page * $this->_limit;
        return $this;
    }

    public function getLimit()
    {
        return $this->_limit;
    }

    public function getOffset()
    {
        return $this->_offset;
    }

    public function fetchAll($assoc = true, &$affectedRows = 0)
    {
        $this->proxyCacheTo($this->_dataSource);
        return $this->_dataSource->limit($this->_limit, $this->_offset)->fetchAll($assoc, $affectedRows);
    }

    public function fetchModels(&$affectedRows = 0)
    {
        $this->proxyCacheTo($this->_dataSource);
        return $this->_dataSource->limit($this->_limit, $this->_offset)->fetchModels($affectedRows);
    }

    public function totalCount()
    {
        return $this->_dataSource->totalCount();
    }
}
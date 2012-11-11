<?php

namespace Magelight\Dbal\Db\Mysql;
/**
 * Abstract Orm
 *
 * @method \Magelight\Dbal\Db\Mysql\Orm     whereEq($expression, $param)
 * @method \Magelight\Dbal\Db\Mysql\Orm     whereNeq($expression, $param)
 * @method \Magelight\Dbal\Db\Mysql\Orm     whereNull($expression)
 * @method \Magelight\Dbal\Db\Mysql\Orm     whereNotNull($expression)
 * @method \Magelight\Dbal\Db\Mysql\Orm     whereGt($expression, $param)
 * @method \Magelight\Dbal\Db\Mysql\Orm     whereGte($expression, $param)
 * @method \Magelight\Dbal\Db\Mysql\Orm     whereLt($expression, $param)
 * @method \Magelight\Dbal\Db\Mysql\Orm     whereLte($expression, $param)
 * @method \Magelight\Dbal\Db\Mysql\Orm     whereLike($expression, $param)
 * @method \Magelight\Dbal\Db\Mysql\Orm     whereIn($expression, $param)
 * @method \Magelight\Dbal\Db\Mysql\Orm     whereNotIn($expression, $param)
 * @method \Magelight\Dbal\Db\Mysql\Orm     whereEx($expression)
 */
class Orm
{
    /**
     * Key constants
     */
    const KEY_EXPRESSION = 0;
    const KEY_OPERATOR = 1;
    const KEY_PARAMS = 2;
    const KEY_PLACEHOLDERS = 3;

    /**
     * Logic contants
     */
    const LOGIC_AND = 'AND';
    const LOGIC_OR = 'OR';
    const LOGIC_AND_NOT = 'AND NOT';
    const LOGIC_OR_NOT = 'OR NOT';

    /**
     * Where map rendering
     *
     * @var array
     */
    protected $_renderWhereMap = [
        'whereEq'        => '=',
        'whereNeq'       => '!=',
        'whereNull'      => 'IS NULL',
        'whereNotNull'   => 'IS NOT NULL',
        'whereGt'        => '>',
        'whereGte'       => '>=',
        'whereLt'        => '<',
        'whereLte'       => '<=',
        'whereLike'      => 'LIKE',
        'whereIn'        => 'IN',
        'whereNotIn'     => 'NOT IN',
        'whereEx'        => '',
    ];

    /**
     * Flag is profiling enabled
     *
     * @var bool
     */
    protected $_profilingEnabled = false;

    /**
     * Profiling array
     *
     * @var array
     */
    protected $_profiling = [];

    /**
     * Where statements
     *
     * @var array
     */
    protected $where = [];

    /**
     * PDO instance
     *
     * @var Adapter
     */
    protected $db;

    /**
     * Model name
     *
     * @var string
     */
    protected $modelName = null;

    /**
     * Current table name
     *
     * @var string
     */
    protected $tableName = null;

    /**
     * array of data
     *
     * @var array
     */
    protected $data = [];

    /**
     * Aliases for data keys
     *
     * @var array
     */
    protected $fieldAliases = [];

    /**
     * Select fields
     *
     * @var array
     */
    protected $selectFields = [];

    /**
     * Identifier column
     *
     * @var string
     */
    protected $idColumn = 'id';

    /**
     * Flag to identify is this data a new record
     *
     * @var bool
     */
    protected $isNewRecord = false;

    /**
     * Dirty fields array
     *
     * @var array
     */
    protected $dirtyFields = [];

    /**
     * Quotation character
     *
     * @var string
     */
    protected $quoteChar = '`';

    /**
     * Internal query string
     *
     * @var string
     */
    protected $query = null;

    /**
     * Internal query params
     *
     * @var array
     */
    protected $params = [];

    /**
     * Internal PDO statememnt object
     *
     * @var \PDOStatement
     */
    protected $statement = null;

    /**
     * Select Limit
     *
     * @var int
     */
    protected $limit = null;

    /**
     * Limit offset
     *
     * @var int
     */
    protected $offset = null;

    /**
     * Group by fields
     *
     * @var array
     */
    protected $groupBy = [];

    /**
     * Order by fields and orders
     *
     * @var array
     */
    protected $orderBy = [];

    /**
     * Table fields from describe table statement
     *
     * @var array
     */
    protected $tableFields = [];

    /**
     * Constructor
     *
     * @param string $tableName
     * @param string $idColumn
     * @param string $modelName
     * @param \Magelight\Dbal\Db\Common\Adapter $db
     */
    public function __construct($tableName = null, $idColumn = null, $modelName = null,
                                \Magelight\Dbal\Db\Common\Adapter $db)
    {
        if (!empty($tableName)) {
            $this->tableName = $tableName;
        }
        $this->db = $db;
        $this->modelName = $modelName;
        $this->idColumn = $idColumn;
    }

    /**
     * Get current orm profile
     *
     * @return array
     */
    public function getProfile()
    {
        return $this->_profiling;
    }

    /**
     * Get if the ORM record is new
     *
     * @return boolean
     */
    public function isNew()
    {
        return $this->isNewRecord;
    }

    /**
     * Get the quotation character
     *
     * @return string|null
     */
    protected function getQuoteChar()
    {
        return $this->quoteChar;
    }

    /**
     * Set the id column
     *
     * @param string $idColumn
     */
    public function setIdColumn($idColumn = 'id')
    {
        if (!empty($idColumn)) {
            $this->idColumn = $idColumn;
        }
    }

    /**
     * Get the id column
     *
     * @return string
     */
    public function getIdColumn()
    {
        return $this->idColumn;
    }

    /**
     * Get identifier
     *
     * @return mixed
     */
    public function getId()
    {
        return isset($this->data[$this->idColumn]) ? $this->data[$this->idColumn] : null;
    }

    /**
     * Mark fields as dirty
     *
     * @param array $fields
     */
    private function markDirty($fields)
    {
        if (!is_array($fields)) {
            $fields = array($fields);
        }
        foreach ($fields as $field) {
            if (!in_array($field, $this->dirtyFields)) {
                $this->dirtyFields[] = $field;
            }
        }
    }

    /**
     * Mark all fields as dirty
     */
    private function markAllDirty()
    {
        if (!empty($this->data)) {
            $this->dirtyFields = array_keys($this->data);
        }
    }

    /**
     * Create ORM entity
     *
     * @param array $data
     * @param bool $forceNew
     */
    public function create($data = [], $forceNew = false)
    {
        $this->isNewRecord = !isset($data[$this->idColumn]) || $forceNew;
        $this->data = $data;
        if ($this->isNewRecord) {
            unset($this->data[$this->idColumn]);
            $this->markAllDirty();
        }
    }

    /**
     * Merge data
     *
     * @param array $data
     * @param bool $overwrite
     * @return Orm
     */
    public function mergeData($data = [], $overwrite = false)
    {
        if (!empty($data) && is_array($data)) {
            if (!is_array($this->data)) {
                $this->data = [];
            }
            if ($overwrite) {
                $this->data = array_merge($this->data, $data);
            } else {
                $this->data += $data;
            }
            $this->markDirty(array_keys($this->data));
        }
        return $this;
    }

    /**
     * Get dirty data
     *
     * @return array
     */
    private function getDirtyData()
    {
        $data = [];
        foreach ($this->dirtyFields as $df) {
            $data[$df] = $this->data[$df];
        }
        return $data;
    }

    /**
     * Push parameters into array
     *
     * @param array $params
     */
    private function pushParams($params = [])
    {
        if (!is_array($params)) {
            $params = [$params];
        }
        foreach ($params as $p) {
            $this->params[] = $p;
        }
    }

    /**
     * Get the select fields for query
     *
     * @return array
     */
    private function getSelectFields()
    {
        $fields = [];
        foreach ($this->selectFields as $selectField) {
            $fields[] = isset($this->fieldAliases[$selectField])
                ? $selectField . ' AS ' . $this->fieldAliases[$selectField]
                : $selectField;
        }
        return $fields;
    }

    /**
     * Magic caller
     *
     * @param string $name
     * @param array $arguments
     * @return Orm
     * @throws \Magelight\Exception
     */
    public function __call($name, $arguments)
    {
        if (strpos($name, 'where') !== false) {
            if (isset($this->_renderWhereMap[$name])) {
                $expression = isset($arguments[0]) ? $arguments[0] : null;
                $params = isset($arguments[1]) ? $arguments[1]  : [];
                $this->where[] = [
                    self::KEY_OPERATOR => $name,
                    self::KEY_EXPRESSION => $expression,
                    self::KEY_PARAMS => $params,
                ];
            }
            return $this;
        }
        $class = __CLASS__;
        throw new \Magelight\Exception("Undefined method call {$class}::{$name}");
    }

    /**
     * Add LIMIT statement to ORM
     *
     * @param int $limit
     * @param int $offset
     *
     * @return Orm
     */
    public function limit($limit, $offset = null)
    {
        $this->limit = $limit;
        $this->offset = $offset;
        return $this;
    }

    /**
     * Add offset statement to ORM
     *
     * @param int $value
     * @return Orm
     */
    public function offset($value)
    {
        $this->offset = $value;
        return $this;
    }

    /**
     * Add GROUP BY ... statement to ORM, string columns as params
     *
     * @return Orm
     */
    public function groupBy()
    {
        $columns = func_get_args();
        if (is_array($columns[0])) {
            $columns = $columns[0];
        }
        foreach ($columns as $col) {
            $this->groupBy[] = $col;
        }
        return $this;
    }

    /**
     * Add ORDER BY statement
     *
     * @param $column
     * @param $order
     *
     * @return Orm
     */
    private function orderBy($column, $order)
    {
        if ('asc' === strtolower($order) || 'desc' === strtolower($order)) {
            if (empty($this->orderBy[$order])) {
                $this->orderBy[$order] = [];
            }
            if (!in_array($column, $this->orderBy[$order])) {
                $this->orderBy[$order][] = $column;
            }
        }
        return $this;
    }

    /**
     * Add ORDER BY ... ASC statement to orm
     *
     * @param mixed $columns
     *
     * @return Orm
     */
    public function orderByAsc($columns)
    {
        $columns = func_get_args();

        if (is_array($columns[0])) {
            $columns = $columns[0];
        }
        foreach ($columns as $col) {
            $this->orderBy($col, 'ASC');
        }
        return $this;
    }

    /**
     * Add ORDER BY ... DESC statement to orm
     *
     * @param mixed $columns
     *
     * @return Orm
     */
    public function orderByDesc($columns)
    {
        $columns = func_get_args();
        if (is_array($columns[0])) {
            $columns = $columns[0];
        }
        foreach ($columns as $col) {
            $this->orderBy($col, 'DESC');
        }
        return $this;
    }

    /**
     * Add select field
     *
     * @param $expression
     * @param null $alias
     * @param array $params
     * @param bool $distinct
     * @return Orm
     */
    public function selectField($expression, $alias = null, $params = [], $distinct = false)
    {
        if (!empty($expression)) {
            $this->selectFields[] = $expression;
            if (!empty($alias)) {
                $this->fieldAliases[$expression] = $alias;
            }
            $this->pushParams($params);
        }
        return $this;
    }

    /**
     * Add select field filter
     *
     * @param array $fields
     * @return Orm
     */
    public function selectFields($fields = [])
    {
        if (empty($fields)) {
            return $this;
        }
        if (!is_array($fields)) {
            $fields = func_get_args();
        }
        foreach ($fields as $field) {
            $this->selectField($field);
        }
        return $this;
    }

    /**
     * Build SELECT statement
     */
    protected function buildSelectStart()
    {
        $query[] = 'SELECT';
        $query[] = empty($this->selectFields) ? '*' : join(', ', $this->getSelectFields());
        $query[] = 'FROM';
        $query[] = $this->tableName;
        return implode(' ', $query);
    }

    /**
     * Build WHERE statement
     *
     */
    protected function buildWhere()
    {
        if (empty($this->where)) {
            return '';
        }
        $query = [];
        foreach ($this->where as $w) {
            if (!isset($this->_renderWhereMap[$w[self::KEY_OPERATOR]])) {
                continue;
            }
            $wherePart = [];
            if (!empty($w[self::KEY_EXPRESSION])) {
                $wherePart[] = $w[self::KEY_EXPRESSION];
            }
            if (!empty($w[self::KEY_OPERATOR])) {
                $wherePart[] = $this->_renderWhereMap[$w[self::KEY_OPERATOR]];
            }
            if (!empty($w[self::KEY_PARAMS])) {
                $wherePart[] = $this->preparePlaceholders($w[self::KEY_PARAMS]);
                $this->pushParams($w[self::KEY_PARAMS]);
            }
            $query[] = '(' . implode(' ', $wherePart) . ')';
        }
        return ' WHERE ' . implode(' AND ', $query);
    }

    /**
     * Prepare placeholders
     *
     * @param string $params
     * @return string
     */
    protected function preparePlaceholders($params)
    {
        if (is_array($params)) {
            return '(' . implode(',', array_fill(0, count($params), '?')) . ')';
        }
        return '?';
    }

    /**
     * Build GROUP BY statement
     *
     */
    protected function buildGroupBy()
    {
        if (empty($this->groupBy)) {
            return null;
        }
        $query[] = 'GROUP BY';
        $query[] = implode(', ', $this->groupBy);
        return implode(' ', $query);
    }

    /**
     * Build ORDER BY statement
     *
     */
    protected function buildOrderBy()
    {
        $orderParts = [];
        if (empty($this->groupBy['ASC']) && empty($this->orderBy['DESC'])) {
            return null;
        }

        $query[] = 'ORDER BY';
        foreach ($this->orderBy as $order => $fields) {
            if (!empty($fields) && is_array($fields)) {
                $orderParts[] = join(', ', $fields) . ' ' . $order;
            }
        }
        $query[] = implode(', ', $orderParts);
        return implode(' ', $query);
    }

    /**
     * Build limit statement
     */
    protected function buildLimit()
    {
        if (empty($this->limit) && $this->limit !== 0) {
            return null;
        }
        $query[] = 'LIMIT';
        if (!empty($this->offset)) {
            $query[] = join(', ', [$this->offset, $this->limit]);
        } else {
            $query[] = $this->limit;
        }
        return implode(' ', $query);
    }

    /**
     * Create placeholders for params
     *
     * @param $params
     * @return null|string
     */
    protected static function createPlaceholders($params)
    {
        if (!is_array($params)) {
            return null;
        }
        $count = count($params);
        if ($count < 1) {
            return null;
        } elseif ($count == 1) {
            return '?';
        }
        return '(' . join(', ', array_fill(0, $count, '?')) . ')';
    }

    /**
     * Build select query
     *
     * @return string
     */
    protected function buidSelect()
    {
        $query = $this->buildSelectStart()
            . ' ' . $this->buildWhere()
            . ' ' . $this->buildGroupBy()
            . ' ' . $this->buildOrderBy()
            . ' ' . $this->buildLimit()
        ;
        return $query;
    }

    /**
     * Build insert statement
     *
     * @param $keys
     * @param bool $ignore
     * @param bool $onDuplicateKeyUpdate
     * @return string
     */
    protected function buildInsert($keys, $ignore = false, $onDuplicateKeyUpdate = false)
    {
        $query[] = 'INSERT';
        if ($ignore) {
            $query[] = 'IGNORE';
        }
        $query[] = 'INTO ' . $this->tableName;
        $query[] = 'SET';
        $values = [];
        foreach ($keys as $key) {
            $values[] = $key . ' = ? ';
        }
        $values = implode(',', $values);
        $query[] = $values;
        if (!$ignore && $onDuplicateKeyUpdate) {
            $query[] = 'ON DUPLICATE KEY UPDATE';
            $query[] = 'SET';
            $query[] = $values;
        }
        return implode(' ', $query);
    }

    /**
     * Build update statement
     *
     * @param $keys
     * @return string
     */
    protected function buildUpdate($keys)
    {
        $query[] = 'UPDATE ' . $this->tableName;
        $query[] = 'SET';
        $fields = [];
        foreach ($keys as $f) {
            $fields[] = $f . ' = ? ';
        }
        $query[] = implode(', ', $fields);
        $query[] = 'WHERE';
        $query[] = $this->idColumn . ' = ? ';
        return implode(' ', $query);
    }

    /**
     * BUild delete query
     *
     * @return string
     */
    protected function buildDelete()
    {
        $query[] = 'DELETE FROM';
        $query[] = $this->tableName;
        $query[] = 'WHERE';
        $query[] = $this->idColumn . ' = ?';
        return implode(' ', $query);
    }

    /**
     * Save record
     */
    public function save($safeMode = false, $ignore = false, $onDuplicateKeyUpdate = false)
    {
        $values = $this->getDirtyData();
        $keys = $this->dirtyFields;
        if ($safeMode) {
            $safeKeys = $this->getTableFields();
            $keys = array_intersect($keys, $this->getTableFields());
            $values = array_intersect_key($values, array_flip($keys));
        }
        if ($this->isNewRecord) {
            $query = $this->buildInsert($keys, $ignore, $onDuplicateKeyUpdate);
        } else {
            $query = $this->buildUpdate($keys);
            $values[$this->idColumn] = $this->data[$this->idColumn];
        }

        $this->statement = $this->db->execute($query, array_values($values));
        $ret = $this->statement->rowCount();
        if ($ret > 0) {
            $this->dirtyFields = [];
            $this->isNewRecord = false;
        }
        return $ret;
    }


    /**
     * Delete record by id or internal id
     *
     * @param mixed $id - identifier
     *
     * @return int|null
     */
    public function delete($id = null)
    {
        if (empty($id) && !isset($this->data[$this->idColumn])) {
            return null;
        }
        if (empty($id)) {
            $id = $this->data[$this->idColumn];
        }
        $this->statement = $this->db->execute($this->buildDelete(), [$id]);
        $ret = $this->statement->rowCount();
        if ($ret > 0) {
            $this->dirtyFields = [];
            $this->isNewRecord = false;
        }
        return $ret;
    }

    /**
     * Fetch row by select query
     *
     * @param bool $array - fetch as array array, else both
     *
     * @return mixed
     */
    public function fetchRow($array = true)
    {
        $this->statement = $this->db->execute($this->buidSelect(), array_values($this->params));
        $data = $this->statement->fetch($array ? \PDO::FETCH_ASSOC : \PDO::FETCH_BOTH);
        if (!empty($data)) {
            $this->data = $data;
        }
        return $data;
    }

    /**
     * Fetch all data by select query
     *
     * @param bool $array - fetch as array array, else both
     *
     * @return array
     */
    public function fetchAll($array = true)
    {
        $this->statement = $this->db->execute($this->buidSelect(), array_values($this->params));
        $data = $this->statement->fetchAll($array ? \PDO::FETCH_ASSOC : \PDO::FETCH_BOTH);
        return $data;
    }

    /**
     * Fetch model
     *
     * @return \Magelight\Model
     */
    public function fetchModel()
    {
        $data = $this->fetchRow(true);
        return empty($data) ? null : $this->createModel($data);
    }

    /**
     * Fetch array of models
     *
     * @return array
     */
    public function fetchModels()
    {
        $dataArray = $this->fetchAll(true);
        $models = [];
        foreach ($dataArray as $data) {
            if (!empty($data)) {
                $models[] = $this->createModel($data);
            }
        }
        return $models;
    }

    /**
     * Get count of rows affected by last query
     *
     * @return int
     */
    public function totalCount()
    {
        return $this->db->execute($this->buidSelect(), array_values($this->params))->rowCount();
    }

    /**
     * Create model instance
     *
     * @param array $data
     * @return \Magelight\Model
     */
    protected function createModel($data)
    {
        if (empty($this->modelName) || !class_exists($this->modelName, true)) {
            return null;
        } else {
            return call_user_func_array([$this->modelName, 'forge'], [$data, false]);
        }
    }

    /**
     * Set the raw query
     *
     * @param $rawQuery
     * @param array $rawParams
     * @return Orm
     */
    public function executeRawQuery($rawQuery, $rawParams = [])
    {
        $this->statement = $this->db->execute($rawQuery, array_values($rawParams));
        return $this;
    }

    /**
     * Setter for data array
     *
     * @param string $name
     * @param mixed $value
     */
    public function setValue($name, $value)
    {
        $this->data[$name] = $value;
        $this->markDirty($name);
    }

    /**
     * Getter for data array
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getValue($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    /**
     * Reset object
     *
     * @return Orm
     */
    public function reset()
    {
        $this->data = [];
        $this->dirtyFields = [];
        $this->where = [];
        $this->statement = null;
        $this->selectFields = [];
        $this->params = [];
        $this->isNewRecord = false;
        $this->query = null;
        $this->fieldAliases = [];
        $this->groupBy = [];
        $this->orderBy = [];
        $this->limit = null;
        $this->offset = null;
        return $this;
    }

    /**
     * Describe table fields
     *
     * @return array
     */
    public function getTableFields()
    {
        if (empty($this->tableFields)) {
            foreach ($this->describeTable() as $value) {
                $this->tableFields[] = $value[0];
            }
        }
        return $this->tableFields;
    }

    /**
     * Get table description
     *
     * @return array
     */
    public function describeTable()
    {
        $this->executeRawQuery('DESCRIBE ' . $this->tableName);
        return $this->statement->fetchAll();
    }

    /**
     * Get table columns
     *
     * @return mixed
     */
    public function getTableColumns()
    {
        $this->executeRawQuery('SHOW COLUMNS FROM ' . $this->tableName);
        return $this->statement->fetchAll();
    }

    /**
     * Get data from ORM
     *
     * @param array $fields
     *
     * @return array
     */
    public function getData($fields = [])
    {
        if (empty($fields)) {
            return $this->data;
        }
        return array_intersect($this->data, array_flip($fields));
    }
}

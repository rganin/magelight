<?php

namespace Magelight\Dbal\Db;
/**
 * Abstract Orm
 */
class AbstractOrm
{
    /**
     * Key constants
     */
    const KEY_EXPRESSION = 0;
    const KEY_OPERATOR = 1;
    const KEY_PARAMS = 2;
    const KEY_PLACEHOLDERS = 3;

    /**
     * Where array
     *
     * @var array
     */
    protected $where = array();

    /**
     * PDO instance
     *
     * @var AbstractAdapter
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
    protected $data = array();

    /**
     * Aliases for data keys
     *
     * @var array
     */
    protected $fieldAliases = array();

    /**
     * Select fields
     *
     * @var array
     */
    protected $selectFields = array();

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
    protected $dirtyFields = array();

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
    protected $params = array();

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
    protected $groupBy = array();

    /**
     * Order by fields and orders
     *
     * @var array
     */
    protected $orderBy = array();

    /**
     * Table fields from describe table statement
     *
     * @var array
     */
    protected $tableFields = array();

    /**
     * Constructor
     *
     * @param null $tableName
     * @param null $idColumn
     * @param null $modelName
     * @param AbstractAdapter $db
     */
    public function __construct($tableName = null, $idColumn = null, $modelName = null, AbstractAdapter $db)
    {
        if (!empty($tableName)) {
            $this->tableName = $tableName;
        }
        $this->db = $db;
        $this->modelName = $modelName;
        $this->idColumn = $idColumn;
        $this->quoteChar = $this->getQuoteChar();
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
        foreach ($fields as $key => $field) {
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
    public function create($data = array(), $forceNew = false)
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
     * @return AbstractOrm
     */
    public function mergeData($data = array(), $overwrite = false)
    {
        if (!empty($data) && is_array($data)) {
            if (!is_array($this->data)) {
                $this->data = array();
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
     * Quote SQL param
     *
     * @param mixed $value
     *
     * @return string
     */
    private function sqlQuote($value)
    {
        if (!empty($value)) {
            $value = $this->quoteChar . $value . $this->quoteChar;
        }
        return $value;
    }

    /**
     * Quote SQL array of params
     *
     * @param array $array
     *
     * @return array|mixed
     */
    private function sqlQuoteArray($array = array())
    {
        if (!empty($array)) {
            foreach ($array as $key => $value) {
                $array[$key] = $this->sqlQuote($value);
            }
        }
        return $array;
    }


    /**
     * Get data
     *
     * @return array
     */
    private function getDirtyData()
    {
        $data = array();
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
    private function pushParams($params = array())
    {
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
        $fields = array();
        foreach ($this->selectFields as $selectField) {
            $fields[] = isset($this->fieldAliases[$selectField])
                ? $this->tableName . '.' . $selectField . ' AS ' . $this->fieldAliases[$selectField]
                : $this->tableName . '.' . $selectField;
        }
        return $fields;
    }

    /**
     * Add where statement
     *
     * @param $expression
     * @param array $params
     * @return AbstractOrm
     */
    private function where($expression, $params = array())
    {
        if (!empty($params) && !is_array($params)) {
            $params = array($params);
        }
        $this->where[] = array(
            self::KEY_EXPRESSION => $expression,
            self::KEY_PARAMS => $params
        );
        return $this;
    }

    /**
     * Add WHERE ... = ? statement to ORM
     *
     * @param $expression
     * @param $params
     * @return AbstractOrm
     */
    public function whereEq($expression, $params) {
        return $this->where("{$expression} = ?", $params);
    }

    /**
     * Add WHERE ... != ? statement to orm
     *
     * @param string $expression
     * @param array $params
     * @return AbstractOrm
     */
    public function whereNeq($expression, $params) {
        return $this->where("{$expression} != ?", $params);
    }

    /**
     * Add WHERE ... LIKE ? statement to ORM
     *
     * @param string $expression
     * @param string $params - 'str' | '%str' | '%str%' | 'str%'
     *
     * @return AbstractOrm
     */
    public function whereLike($expression, $params) {
        return $this->where("{$expression} LIKE ?", $params);
    }

    /**
     * Add WHERE ... > ? statement to ORM
     *
     * @param string $expression
     * @param array $params
     *
     * @return AbstractOrm
     */
    public function whereGt($expression, $params) {
        return $this->where("{$expression} > ?", $params);
    }
    /**
     * Add WHERE ... >= ? statement to ORM
     *
     * @param string $expression
     * @param array $params
     *
     * @return AbstractOrm
     */
    public function whereGte($expression, $params) {
        return $this->where("{$expression} >= ?", $params);
    }

    /**
     * Add WHERE ... >= ? statement to ORM
     *
     * @param string $expression
     * @param array $params
     *
     * @return AbstractOrm
     */
    public function whereLt($expression, $params) {
        return $this->where("{$expression} < ?", $params);
    }

    /**
     * Add WHERE ... <= ? statement to ORM
     *
     * @param string $expression
     * @param array $params
     *
     * @return AbstractOrm
     */
    public function whereLte($expression, $params) {
        return $this->where("{$expression} <= ?", $params);
    }

    /**
     * Add WHERE ... IS NULL statement to ORM
     *
     * @param string $expression
     *
     * @return AbstractOrm
     */
    public function whereNull($expression) {
        return $this->where("{$expression} IS NULL");
    }

    /**
     * Add WHERE ... IS NOT NULL statement to ORM
     *
     * @param string $expression
     *
     * @return AbstractOrm
     */
    public function whereNotNull($expression) {
        return $this->where("{$expression} IS NOT NULL");
    }

    /**
     * Add WHERE raw-statement to ORM
     *
     * @param string $expression
     * @param array $params
     *
     * @return AbstractOrm
     */
    public function whereRaw($expression, $params = array()) {
        return $this->where($expression, $params);
    }

    /**
     * Check the fields are equal to the given parameters
     *
     * @param array $fields - array of fields
     * @param array $parameters - array of values to compare with
     * @param string $logic - AND|OR
     *
     * @return AbstractOrm
     */
    public function whereEqualArray($fields=array(), $parameters=array(), $logic = 'OR') {
        return $this->whereOptArray($fields, $parameters, $logic, ' = ');
    }

    /**
     * Add where optional array with the desired logic
     *
     * @param array $fields
     * @param array $parameters
     * @param string $logic
     * @param string $comparison
     *
     * @return AbstractOrm
     */
    protected function whereOptArray($fields=array(), $parameters=array(), $logic = 'OR', $comparison = ' = ') {
        $conds = array();

        foreach ($fields as $f) {
            $conds[] = " {$f} {$comparison} ? ";
        }
        $conds = implode(" {$logic} ", $conds);
        return $this->whereRaw($conds, $parameters);
    }

    /**
     * Add LIMIT statement to ORM
     *
     * @param int $limit
     * @param int $offset
     *
     * @return AbstractOrm
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
     * @return AbstractOrm
     */
    public function offset($value)
    {
        $this->offset = $value;
        return $this;
    }

    /**
     * Add GROUP BY ... statement to ORM
     *
     * @param mixed $columns - [..., ...]
     *
     * @return AbstractOrm
     */
    public function groupBy($columns)
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
     * @return AbstractOrm
     */
    private function orderBy($column, $order)
    {
        if ('asc' === strtolower($order) || 'desc' === strtolower($order)) {
            if (empty($this->orderBy[$order])) {
                $this->orderBy[$order] = array();
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
     * @return AbstractOrm
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
     * @return AbstractOrm
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
     * Alias for distinct columns
     *
     * @param string $column
     *
     * @return AbstractOrm
     */
    public function distinct($column = null)
    {
        return $this->selectField($column, null, array(), true);
    }

    /**
     * Add select field
     *
     * @param $expression
     * @param null $alias
     * @param array $params
     * @param bool $distinct
     * @return AbstractOrm
     */
    public function selectField($expression, $alias = null, $params = array(), $distinct = false)
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
     * @return AbstractOrm
     */
    public function selectFields($fields = array())
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
    private function buildSelectStart()
    {
        $query[] = 'SELECT';
        $query[] = empty($this->selectFields) ? $this->tableName . '.*' : join(', ', $this->getSelectFields());
        $query[] = 'FROM';
        $query[] = $this->tableName;
        return implode(' ', $query);
    }

    /**
     * Build WHERE statement
     *
     */
    private function buildWhere()
    {
        if (empty($this->where)) {
            return null;
        }
        $query = array();
        foreach ($this->where as $w) {
            $wherePart = array();
            if (!empty($w[self::KEY_EXPRESSION])) {
                $wherePart[] = $w[self::KEY_EXPRESSION];
            }
            if (!empty($w[self::KEY_PARAMS])) {
                $this->pushParams($w[self::KEY_PARAMS]);
            }
            $query[] = '(' . implode(' ', $wherePart) . ')';
        }
        return ' WHERE ' . implode(' AND ', $query);
    }

    /**
     * Build GROUP BY statement
     *
     */
    private function buildGroupBy()
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
    private function buildOrderBy()
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
    private function buildLimit()
    {
        if (empty($this->limit) && $this->limit !== 0) {
            return null;
        }
        $query[] = 'LIMIT';
        if (!empty($this->offset)) {
            $query[] = join(', ', array($this->offset, $this->limit));
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
    private static function createPlaceholders($params)
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
    private function buidSelect()
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
    private function buildInsert($keys, $ignore = false, $onDuplicateKeyUpdate = false)
    {
        $query[] = 'INSERT';
        if ($ignore) {
            $query[] = 'IGNORE';
        }
        $query[] = 'INTO ' . $this->tableName;
        $query[] = 'SET';
        $values = array();
        foreach ($keys as $key) {
            $values = $key . ' = ? ';
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
    private function buildUpdate($keys)
    {
        $query[] = 'UPDATE ' . $this->tableName;
        $query[] = 'SET';
        $fields = array();
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
    private function buildDelete()
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

        $this->execute($query, $values);
        $ret = $this->statement->rowCount();
        if ($ret > 0) {
            $this->dirtyFields = array();
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
        $this->execute($this->buildDelete(), array($id));
        $ret = $this->statement->rowCount();
        if ($ret > 0) {
            $this->dirtyFields = array();
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
        $this->execute($this->buidSelect(), $this->params);
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
        $this->execute($this->buidSelect(), $this->params);
        $data = $this->statement->fetchAll($array ? \PDO::FETCH_ASSOC : \PDO::FETCH_BOTH);
        if (!empty($data)) {
            $this->data = $data;
        }
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
        $models = array();
        foreach ($dataArray as $data) {
            if (!empty($data)) {
                $models[] = $this->createModel($data);
            }
        }
        return $models;
    }

    /**
     * Get count of rows
     *
     * @param int $pushToVar
     * @return int
     */
    public function totalCount(&$pushToVar = null)
    {
        $args = func_get_args();
        $this->execute($this->buidSelect(), $this->params);
        if (count($args) > 0) {
            $pushToVar = $this->statement->rowCount();
            return $this;
        }
        return $this->statement->rowCount();
    }

    /**
     * Get count of rows affected by last query
     *
     * @param int $pushToVar
     * @return int
     */
    public function lastQueryRowsCount(&$pushToVar = null)
    {
        $args = func_get_args();
        if (count($args) > 0) {
            $pushToVar = $this->statement->rowCount();
            return $this;
        }
        return $this->statement->rowCount();
    }

    /**
     * Create model instance
     *
     * @param array $data
     * @return \Magelight\Model
     */
    private function createModel($data)
    {
        if (empty($this->modelName) || !class_exists($this->modelName, true)) {
            return null;
        } else {
            return call_user_func_array(array($this->modelName, 'forge'), array($data, false));
        }
    }

    /**
     * Execute query
     *
     * @param $query
     * @param array $params
     */
    protected function execute($query, $params = array())
    {
        $this->statement = $this->db->prepare($query);
        if (!$this->statement->execute(array_values($params))) {
            $errorInfo = $this->statement->errorInfo();
            //\Errors::push(isset($errorInfo[2]) ? $errorInfo[2] : 'unknown error', null, \Errors::ERROR_CLASS_DB, true);
        };
    }

    /**
     * Set the raw query
     *
     * @param $rawQuery
     * @param array $rawParams
     * @return AbstractOrm
     */
    public function executeRawQuery($rawQuery, $rawParams = array())
    {
        $this->execute($rawQuery, $rawParams);
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
     * @return AbstractOrm
     */
    public function reset()
    {
        $this->data = array();
        $this->dirtyFields = array();
        $this->where = array();
        $this->statement = null;
        $this->selectFields = array();
        $this->params = array();
        $this->isNewRecord = false;
        $this->query = null;
        $this->fieldAliases = array();
        $this->groupBy = array();
        $this->orderBy = array();
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
        $statement = $this->db->prepare('DESCRIBE ' . $this->tableName);
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * Get data from ORM
     *
     * @param array $fields
     *
     * @return array
     */
    public function getData($fields = array())
    {
        if (empty($fields)) {
            return $this->data;
        }
        return array_intersect($this->data, array_flip($fields));
    }
}

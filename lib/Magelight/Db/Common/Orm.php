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

namespace Magelight\Db\Common;
use Magelight\Db\Common\Expression\Expression;
use Magelight\Db\Common\Expression\ExpressionInterface;
use Magelight\Exception;

/**
 * Abstract Orm
 *
 * @method $this joinLeft($table, $alias = null, $onStatement = null, $onParams = [])
 * @method $this joinRight($table, $alias = null, $onStatement = null, $onParams = [])
 * @method $this joinCross($table, $alias = null, $onStatement = null, $onParams = [])
 * @method $this joinInner($table, $alias = null, $onStatement = null, $onParams = [])
 * @method $this joinOuterLeft($table, $alias = null, $onStatement = null, $onParams = [])
 * @method $this joinOuterRight($table, $alias = null, $onStatement = null, $onParams = [])
 *
 * @method $this whereEq($expression, $param)
 * @method $this whereNeq($expression, $param)
 * @method $this whereNull($expression)
 * @method $this whereNotNull($expression)
 * @method $this whereGt($expression, $param)
 * @method $this whereGte($expression, $param)
 * @method $this whereLt($expression, $param)
 * @method $this whereLte($expression, $param)
 * @method $this whereLike($expression, $param)
 * @method $this whereIn($expression, $param)
 * @method $this whereNotIn($expression, $param)
 * @method $this whereBetween($expression, $paramsLowAndHigh)
 * @method $this whereEx(ExpressionInterface $expression)
 *
 * @method $this orWhereEq($expression, $param)
 * @method $this orWhereNeq($expression, $param)
 * @method $this orWhereNull($expression)
 * @method $this orWhereNotNull($expression)
 * @method $this orWhereGt($expression, $param)
 * @method $this orWhereGte($expression, $param)
 * @method $this orWhereLt($expression, $param)
 * @method $this orWhereLte($expression, $param)
 * @method $this orWhereLike($expression, $param)
 * @method $this orWhereIn($expression, $param)
 * @method $this orWhereNotIn($expression, $param)
 * @method $this orWhereBetween($expression, $paramsLowAndHigh)
 * @method $this orWhereEx(ExpressionInterface $expression)
 *
 * @method $this andWhereEq($expression, $param)
 * @method $this andWhereNeq($expression, $param)
 * @method $this andWhereNull($expression)
 * @method $this andWhereNotNull($expression)
 * @method $this andWhereGt($expression, $param)
 * @method $this andWhereGte($expression, $param)
 * @method $this andWhereLt($expression, $param)
 * @method $this andWhereLte($expression, $param)
 * @method $this andWhereLike($expression, $param)
 * @method $this andWhereIn($expression, $param)
 * @method $this andWhereNotIn($expression, $param)
 * @method $this andWhereBetween($expression, $paramsLowAndHigh)
 * @method $this andWhereEx(ExpressionInterface $expression)
 *
 * @method $this andNotWhereEq($expression, $param)
 * @method $this andNotWhereNeq($expression, $param)
 * @method $this andNotWhereNull($expression)
 * @method $this andNotWhereNotNull($expression)
 * @method $this andNotWhereGt($expression, $param)
 * @method $this andNotWhereGte($expression, $param)
 * @method $this andNotWhereLt($expression, $param)
 * @method $this andNotWhereLte($expression, $param)
 * @method $this andNotWhereLike($expression, $param)
 * @method $this andNotWhereIn($expression, $param)
 * @method $this andNotWhereNotIn($expression, $param)
 * @method $this andNotWhereBetween($expression, $paramsLowAndHigh)
 * @method $this andNotWhereEx(ExpressionInterface $expression)
 *
 * @method $this orNotWhereEq($expression, $param)
 * @method $this orNotWhereNeq($expression, $param)
 * @method $this orNotWhereNull($expression)
 * @method $this orNotWhereNotNull($expression)
 * @method $this orNotWhereGt($expression, $param)
 * @method $this orNotWhereGte($expression, $param)
 * @method $this orNotWhereLt($expression, $param)
 * @method $this orNotWhereLte($expression, $param)
 * @method $this orNotWhereLike($expression, $param)
 * @method $this orNotWhereIn($expression, $param)
 * @method $this orNotWhereNotIn($expression, $param)
 * @method $this orNotWhereBetween($expression, $paramsLowAndHigh)
 * @method $this orNotWhereEx(ExpressionInterface $expression)
 */
abstract class Orm
{
    /**
     * Use forgery
     */
    use \Magelight\Traits\TForgery;

    /**
     * Use caching trait
     */
    use \Magelight\Traits\TCache;

    /**
     * Key constants
     */
    const KEY_EXPRESSION   = 0;
    const KEY_OPERATOR     = 1;
    const KEY_PARAMS       = 2;
    const KEY_PLACEHOLDERS = 3;
    const KEY_LOGIC        = 4;

    /**
     * Logic contants
     */
    const LOGIC_AND     = 'AND';
    const LOGIC_OR      = 'OR';
    const LOGIC_AND_NOT = 'AND NOT';
    const LOGIC_OR_NOT  = 'OR NOT';

    /**
     * Order constants
     */
    const ORDER_ASC     = 'ASC';
    const ORDER_DESC    = 'DESC';

    /**
     * PDO instance
     *
     * @var Adapter
     */
    protected $db;

    /**
     * Identifier column
     *
     * @var string
     */
    protected $idColumn = 'id';

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
     * Where map rendering
     *
     * @var array
     */
    protected static $renderWhereMap = [
        'whereEq'        => ['operator' => '=',           'argcount' => 1],
        'whereNeq'       => ['operator' => '=',           'argcount' => 1],
        'whereNull'      => ['operator' => 'IS NULL',     'argcount' => 0],
        'whereNotNull'   => ['operator' => 'IS NOT NULL', 'argcount' => 0],
        'whereGt'        => ['operator' => '>',           'argcount' => 1],
        'whereGte'       => ['operator' => '>=',          'argcount' => 1],
        'whereLt'        => ['operator' => '<',           'argcount' => 1],
        'whereLte'       => ['operator' => '<=',          'argcount' => 1],
        'whereLike'      => ['operator' => 'LIKE',        'argcount' => 1],
        'whereIn'        => ['operator' => 'IN',          'argcount' => 1],
        'whereNotIn'     => ['operator' => 'NOT IN',      'argcount' => 1],
        'whereBetween'   => ['operator' => 'BETWEEN',     'argcount' => 1],
        'whereEx'        => ['operator' => '',            'argcount' => 1],
    ];

    /**
     * Render map for joins
     *
     * @var array
     */
    protected static $renderJoinMap = [
        'joinLeft'       => 'LEFT JOIN',
        'joinRight'      => 'RIGHT JOIN',
        'joinCross'      => 'CROSS JOIN',
        'joinInner'      => 'INNER JOIN',
        'joinOuter'      => 'OUTER JOIN',
    ];

    /**
     * Flag is profiling enabled
     *
     * @var bool
     */
    protected $profilingEnabled = false;

    /**
     * Profiling array
     *
     * @var array
     */
    protected $profiling = [];

    /**
     * Where statements
     *
     * @var array
     */
    protected $where = [];

    /**
     * Cache key
     *
     * @var null|string
     */
    protected $cacheKey = null;

    /**
     * Cache index to use
     *
     * @var string
     */
    protected $cacheIndex = \Magelight\App::DEFAULT_INDEX;

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
     * Total count of records found in data source
     *
     * @var null|int
     */
    protected $totalCount = null;

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
     * Joins
     *
     * @var array
     */
    protected $join = [];

    /**
     * Get DB adapter
     *
     * @return Adapter
     */
    public function getAdapter()
    {
        return $this->db;
    }

    /**
     * Get Orm class by type
     *
     * @param string $type
     * @return string
     */
    public static function getOrmClassByType($type)
    {
        return '\\Magelight\\Db\\' . ucfirst(strtolower($type)) . '\\Orm';
    }

    /**
     * Get WHERE statement render map
     *
     * @return array
     */
    public static function getRenderWhereMap()
    {
        return self::$renderWhereMap;
    }

    /**
     * Get JOIN statement render map
     *
     * @return array
     */
    public static function getRenderJoinMap()
    {
        return self::$renderJoinMap;
    }

    /**
     * Set table name
     *
     * @param string $tableName
     * @return Orm
     */
    public function setTableName($tableName)
    {
        if (!empty($tableName)) {
            $this->tableName = $tableName;
        }
        return $this;
    }

    /**
     * Set model name
     *
     * @param string $modelName
     * @return Orm
     */
    public function setModelName($modelName)
    {
        $this->modelName = $modelName;
        return $this;
    }

    /**
     * Constructor
     * @param \Magelight\Db\Common\Adapter $db
     */
    public function __forge(\Magelight\Db\Common\Adapter $db)
    {
        $this->db = $db;
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
     * Get current orm profile
     *
     * @return array
     */
    public function getProfile()
    {
        return $this->getAdapter()->getProfiler()->getProfile();
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
     * Unmark dirty field
     *
     * @param string $field
     * @return $this
     */
    protected function unmarkDirty($field)
    {
        foreach ($this->dirtyFields as $key => $fieldName) {
            if ($field === $fieldName) {
                unset ($this->dirtyFields[$key]);
            }
        }
        return $this;
    }

    /**
     * Create ORM entity
     *
     * @param array $data
     * @param bool $forceNew
     *
     * @return Orm
     */
    public function create($data = [], $forceNew = false)
    {
        $this->reset();
        $this->isNewRecord = $forceNew || !isset($data[$this->idColumn]) || empty($data[$this->idColumn]);
        $this->data = $data;
        $this->markAllDirty();
        return $this;
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
            $data[$df] = ($df === $this->idColumn && empty($this->data[$df])) ? null : $this->data[$df];
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
        foreach ($this->selectFields as $alias => $selectField) {
            // preparing select part with fields
            // if field is an exression fetch parameters from it as further it will be stringified
            if ($selectField instanceof Expression) {
                $this->pushParams($selectField->getParams());
            }
            $fields[] = is_string($alias)
                ? $selectField . ' AS ' . $alias
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
        if (stripos($name, 'where') !== false) {
            return $this->addWhereStatement($name, $arguments);
        } elseif (stripos($name, 'join') !== false) {
            return $this->addJoinStatement($name, $arguments);
        }

        $class = __CLASS__;
        throw new \Magelight\Exception("Undefined method call {$class}::{$name}");
    }

    /**
     * add statement for join*****($table, $alias, $onStatement, $onParams)
     *
     * @param string $name
     * @param array $arguments
     *
     * @return Orm
     */
    protected function addJoinStatement($name, $arguments)
    {
        if (isset(self::$renderJoinMap[$name])) {
            $join = [
                'logic'  => self::$renderJoinMap[$name],
                'table'  => $arguments[0],
                'alias'  => $arguments[1],
                'on'     => (isset($arguments[2]) ? $arguments[2] : 'TRUE'),
                'params' => isset($arguments[3]) ? $arguments[3] : [],
            ];
            $this->join[] = $join;
            $this->totalCount = null;
        }
        return $this;
    }

    /**
     * Build joins
     *
     * @return string
     */
    protected function buildJoins()
    {
        $query = [];
        foreach ($this->join as $join) {
            $query[] = $join['logic'];
            $query[] = $join['table'];
            if (!empty($join['alias'])) {
                $query[] = $join['alias'];
            }
            $query[] = 'ON ' . $join['on'];
            $this->pushParams($join['params']);
        }
        return implode(' ', $query);
    }

    /**
     * Build and add where *** statement to SQL
     *
     * @param string $name - called method name
     * @param array $arguments - method args
     * @return Orm
     */
    protected function addWhereStatement($name, $arguments)
    {
        $logic = $this->parseStatementLogic($name);
        if ($logic !== null) {
            if (isset(self::$renderWhereMap[$logic['method']])) {
                $expression = isset($arguments[0]) ? $arguments[0] : null;
                if ($expression instanceof ExpressionInterface) {
                    $params = $expression->getParams();
                } else {
                    $params = array_key_exists(1, $arguments) ? $arguments[1]  : [];
                }
                $this->where[] = [
                    self::KEY_OPERATOR => $logic['method'],
                    self::KEY_EXPRESSION => $expression,
                    self::KEY_PARAMS => $params,
                    self::KEY_LOGIC => $logic['logic'],
                ];
                $this->totalCount = null;
            }
        }
        return $this;
    }

    /**
     * Parse statement logic
     *
     * @param $name
     * @return array|null
     */
    protected function parseStatementLogic($name)
    {
        if (substr($name, 0, 5) === 'where') {
            return ['method' => $name, 'logic' => self::LOGIC_AND];
        } elseif (substr($name, 0, 7) === 'orWhere') {
            return ['method' => lcfirst(substr($name, 2)), 'logic' => self::LOGIC_OR];
        } elseif (substr($name, 0, 8) === 'andWhere') {
            return ['method' => lcfirst(substr($name, 3)), 'logic' => self::LOGIC_AND];
        } elseif (substr($name, 0, 10) === 'orNotWhere') {
            return ['method' => lcfirst(substr($name, 5)), 'logic' => self::LOGIC_OR_NOT];
        } elseif (substr($name, 0, 11) === 'andNotWhere') {
            return ['method' => lcfirst(substr($name, 6)), 'logic' => self::LOGIC_AND_NOT];
        }
        return null;
    }

    /**
     * Check is a function call a where statement
     *
     * @param string $name
     * @return bool
     */
    public static function isWhereStatement($name)
    {
        return
           substr($name, 0, 5)  === 'where'
        || substr($name, 0, 7)  === 'orWhere'
        || substr($name, 0, 8)  === 'andWhere'
        || substr($name, 0, 10) === 'orNotWhere'
        || substr($name, 0, 11) === 'andNotWhere';
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
        $this->totalCount = null;
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
     * Add select field.
     * Use Expression as select field to push params for expression.
     *
     * @param string|ExpressionInterface $expression
     * @param null $alias
     * @return Orm
     */
    public function selectField($expression, $alias = null)
    {
        if (!empty($expression)) {
            if (!empty($alias)) {
                $this->selectFields[$alias] = $expression;
            } else {
                $this->selectFields[] = $expression;
            }
        }
        return $this;
    }

    /**
     * Set select field filter
     *
     * @param array $fields
     * @deprecated
     * @return $this
     */
    public function selectFields($fields = [])
    {
        $this->selectFields = [];
        $this->addSelectFields($fields);
        return $this;
    }

    /**
     * Set select fields
     *
     * @param array $fields
     * @return $this
     */
    public function setSelectFields($fields = [])
    {
        return $this->selectFields($fields);
    }

    /**
     * Add fields to select query
     *
     * @param array $fields
     * @return $this
     */
    public function addSelectFields($fields = [])
    {
        if (empty($fields)) {
            return $this;
        }
        if (!is_array($fields)) {
            $fields = func_get_args();
        }
        foreach ($fields as $key => $field) {
            $this->selectField($field, is_string($key) ? $key : null);
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
     * Build SELECT statement
     */
    protected function buildCountSelectStart()
    {
        $query[] = 'SELECT';
        $query[] = 'COUNT(*)';
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
        $count = 0;
        foreach ($this->where as $w) {
            if (!isset(self::$renderWhereMap[$w[self::KEY_OPERATOR]])) {
                continue;
            }
            $wherePart = [];
            if (!empty($w[self::KEY_EXPRESSION])) {
                $wherePart[] = (string)$w[self::KEY_EXPRESSION];
            }
            if (!empty($w[self::KEY_OPERATOR])) {
                $wherePart[] = self::$renderWhereMap[$w[self::KEY_OPERATOR]]['operator'];
            }
            if (array_key_exists(self::KEY_PARAMS, $w) && self::$renderWhereMap[$w[self::KEY_OPERATOR]]['argcount']) {
                if (!($w[self::KEY_EXPRESSION] instanceof ExpressionInterface)) {
                    $wherePart[] = $this->preparePlaceholders($w[self::KEY_PARAMS]);
                }
                $this->pushParams($w[self::KEY_PARAMS]);
            }
            $query[] =
                ($count > 0
                    ? ' ' . $w[self::KEY_LOGIC] . ' '
                    : '') . '(' . implode(' ', $wherePart) . ')';
            $count++;
        }
        return ' WHERE ' . implode(' ', $query);
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
        if (empty($this->orderBy['ASC']) && empty($this->orderBy['DESC'])) {
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
        if (empty($this->limit) || $this->limit == 0) {
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
    protected function buildSelect()
    {
        $this->params = [];
        $query = $this->buildSelectStart()
            . ' ' . $this->buildJoins()
            . ' ' . $this->buildWhere()
            . ' ' . $this->buildGroupBy()
            . ' ' . $this->buildOrderBy()
            . ' ' . $this->buildLimit()
        ;
        return $query;
    }


    /**
     * Build select query
     *
     * @return string
     */
    protected function buildCountSelect()
    {
        $this->params = [];
        $query = $this->buildCountSelectStart()
            . ' ' . $this->buildJoins()
            . ' ' . $this->buildWhere()
            . ' ' . $this->buildGroupBy()
        ;
        return $query;
    }

    /**
     * Get select query
     *
     * @return string
     */
    public function getSelectQuery()
    {
        return $this->buildSelect();
    }

    /**
     * Get params of query that were collected on query build
     *
     * @return array
     */
    public function getQueryParams()
    {
        return $this->params;
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
        $this->params = [];
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
        $this->params = [];
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
     * Build delete query
     *
     * @return string
     */
    protected function buildDelete()
    {
        $this->params = [];
        $query[] = 'DELETE FROM';
        $query[] = $this->tableName;
        $query[] = $this->buildWhere();
        return implode(' ', $query);
    }


    /**
     * Save record
     *
     * @param bool|false $safeMode - put only table fields into insert/update query
     * @param bool|false $ignore - ignore if entity with same ID exists
     * @param bool|false $onDuplicateKeyUpdate - save new row or update if row with same primary ID exists
     * @return bool
     * @throws \Magelight\Exception
     */
    public function save($safeMode = false, $ignore = false, $onDuplicateKeyUpdate = false)
    {
        $values = $this->getDirtyData();
        $keys = $this->dirtyFields;
        if ($safeMode) {
            $keys = array_intersect($keys, $this->getTableFields());
            $values = array_intersect_key($values, array_flip($keys));
        }
        if ($this->isNewRecord) {
            if ($onDuplicateKeyUpdate) {
                $duplicatedValues = $values;
                foreach ($values as $value) {
                    $duplicatedValues[] = $value;
                }
                $values = $duplicatedValues;
            }
            $query = $this->buildInsert($keys, $ignore, $onDuplicateKeyUpdate);
        } else {
            $query = $this->buildUpdate($keys);
            $values[] = $this->data[$this->idColumn];
        }

        $this->statement = $this->db->execute($query, array_values($values));
        $ret = $this->statement->rowCount();
        if ($ret > 0) {
            // if record has ID column and it's forced to be new and no ID is set
            if ($this->idColumn && $this->isNew() && empty($this->getId())) {
                // set it's ID to last autoincrement
                $this->setValue($this->idColumn, $this->db->execute("SELECT LAST_INSERT_ID();")->fetchColumn(0));
            }
            $this->dirtyFields = [];
            $this->isNewRecord = false;
        }
        return true;
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
        return $this->deleteBy($this->idColumn, $id);
    }

    /**
     * Delete by field value
     *
     * @param string $field
     * @param mixed $value
     * @return int|null
     */
    public function deleteBy($field, $value)
    {
        if (empty($field) || empty($value)) {
            return null;
        }
        $this->whereEq($field, $value);
        $this->statement = $this->db->execute($this->buildDelete(), $this->params);
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
        if ($this->getCacheKey()) {
            if ($data = $this->cache()->get($this->buildCacheKey([$this->getCacheKey(), 'row']), false)) {
                return $data;
            }
        }
        $this->statement = $this->db->execute($this->buildSelect(), array_values($this->params));
        $data = $this->statement->fetch($array ? \PDO::FETCH_ASSOC : \PDO::FETCH_BOTH);
        if (!empty($data)) {
            $this->data = $data;
            if ($this->getCacheKey()) {
                $this->cache()->set(
                    $this->buildCacheKey([$this->getCacheKey(), 'row']), $data, $this->getCacheTtl()
                );
            }
        }
        return $data;
    }

    /**
     * Fetch row by select query
     *
     * @param int $columnIndex
     *
     * @return mixed
     */
    public function fetchColumn($columnIndex = 0)
    {
        if ($this->getCacheKey()) {
            if ($data = $this->cache()->get($this->buildCacheKey([$this->getCacheKey(), 'col']), false)) {
                return $data;
            }
        }
        $this->statement = $this->db->execute($this->buildSelect(), array_values($this->params));
        $data = $this->statement->fetchAll(\PDO::FETCH_COLUMN, $columnIndex);
        if ($this->getCacheKey()) {
            $this->cache()->set(
                $this->buildCacheKey([$this->getCacheKey(), 'col']), $data, $this->getCacheTtl()
            );
        }
        return $data;
    }

    /**
     * Fetch first column element
     *
     * @return mixed
     */
    public function fetchFirstColumnElement()
    {
        if ($this->getCacheKey()) {
            if ($data = $this->cache()->get($this->buildCacheKey([$this->getCacheKey(), 'firstCol']), false)) {
                return $data;
            }
        }
        $this->statement = $this->db->execute($this->buildSelect(), array_values($this->params));
        $data = $this->statement->fetchColumn();
        if ($this->getCacheKey()) {
            $this->cache()->set(
                $this->buildCacheKey([$this->getCacheKey(), 'firstCol']), $data, $this->getCacheTtl()
            );
        }
        return $data;
    }

    /**
     * Fetch all data by select query
     *
     * @param bool $array - fetch as array array, else both
     * @param int &$affectedRows - total count
     * @return array
     */
    public function fetchAll($array = true, &$affectedRows = 0)
    {
        if ($this->getCacheKey()) {
            if ($data = $this->cache()->get($this->buildCacheKey([$this->getCacheKey(), 'all']), false)) {
                return $data;
            }
        }
        $this->statement = $this->db->execute($this->buildSelect(), array_values($this->params));
        $affectedRows = $this->statement->rowCount();
        $data = $this->statement->fetchAll($array ? \PDO::FETCH_ASSOC : \PDO::FETCH_BOTH);
        if ($this->getCacheKey()) {
            $this->cache()->set(
                $this->buildCacheKey([$this->getCacheKey(), 'all']), $data, $this->getCacheTtl()
            );
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
     * @param int &$affectedRows
     * @return array
     */
    public function fetchModels(&$affectedRows = 0)
    {
        $dataArray = $this->fetchAll(true, $affectedRows);
        $models = [];
        foreach ($dataArray as $data) {
            if (!empty($data)) {
                $models[] = $this->createModel($data);
            }
        }
        return $models;
    }

    /**
     * Get count of rows by last query
     *
     * @return int
     */
    public function totalCount()
    {
        $this->limit(null, null);
        $this->totalCount = $this->db->execute($this->buildSelect(), array_values($this->params))->rowCount();
        return $this->totalCount;
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
            $model = call_user_func_array([$this->modelName, 'forge'], [$data, false]);
            $model->afterLoad();
            return $model;
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
    public function &getValue($name)
    {
        return $this->data[$name];
    }

    /**
     * Unset orm data value
     *
     * @param string $name
     * @return Orm
     */
    public function unsetValue($name)
    {
        unset($this->data[$name]);
        $this->unmarkDirty($name);
        return $this;
    }

    /**
     * Reset object
     *
     * @return Orm
     */
    public function reset()
    {
        $this->data         =
        $this->dirtyFields  =
        $this->where        =
        $this->selectFields =
        $this->params       =
        $this->groupBy      =
        $this->orderBy      = [];

        $this->limit        =
        $this->offset       =
        $this->query        =
        $this->statement    = null;

        $this->isNewRecord  = false;

        return $this;
    }

    /**
     * Reset sorting order
     *
     * @retrun $this
     */
    public function resetOrderBy()
    {
        $this->orderBy = [];
        return $this;
    }

    /**
     * Reset group by statements
     *
     * @return $this
     */
    public function resetGroupBy()
    {
        $this->groupBy = [];
        return $this;
    }

    /**
     * Reset where statements
     *
     * @retrun $this
     */
    public function resetWhere()
    {
        $this->where = [];
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
            $table = $this->describeTable();
            foreach ($table as $value) {
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

    /**
     * Begin transaction
     *
     * @return Orm
     */
    public function beginTransaction()
    {
        $this->db->beginTransaction();
        return $this;
    }

    /**
     * Commit transaction
     *
     * @return Orm
     */
    public function commitTransaction()
    {
        $this->db->commitTransaction();
        return $this;
    }

    /**
     * Rollback transaction
     *
     * @return Orm
     */
    public function rollbackTransaction()
    {
        $this->db->rollbackTransaction();
        return $this;
    }

    /**
     * Isset magic
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }
}

<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 02.12.12
 * Time: 1:18
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Dbal\Db\Common;

/**
 * Abstract Orm
 *
 * @method \Magelight\Dbal\Db\Mysql\Orm     joinLeft($table, $alias, $onStatement, $onParams)
 * @method \Magelight\Dbal\Db\Mysql\Orm     joinRight($table, $alias, $onStatement, $onParams)
 * @method \Magelight\Dbal\Db\Mysql\Orm     joinCross($table, $alias, $onStatement, $onParams)
 * @method \Magelight\Dbal\Db\Mysql\Orm     joinInnerLeft($table, $alias, $onStatement, $onParams)
 * @method \Magelight\Dbal\Db\Mysql\Orm     joinInnerRight($table, $alias, $onStatement, $onParams)
 * @method \Magelight\Dbal\Db\Mysql\Orm     joinOuterLeft($table, $alias, $onStatement, $onParams)
 * @method \Magelight\Dbal\Db\Mysql\Orm     joinOuterRight($table, $alias, $onStatement, $onParams)
 *
 * @method \Magelight\Dbal\Db\Common\Orm     whereEq($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     whereNeq($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     whereNull($expression)
 * @method \Magelight\Dbal\Db\Common\Orm     whereNotNull($expression)
 * @method \Magelight\Dbal\Db\Common\Orm     whereGt($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     whereGte($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     whereLt($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     whereLte($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     whereLike($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     whereIn($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     whereNotIn($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     whereEx($expression, $params)
 *
 * @method \Magelight\Dbal\Db\Common\Orm     orWhereEq($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     orWhereNeq($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     orWhereNull($expression)
 * @method \Magelight\Dbal\Db\Common\Orm     orWhereNotNull($expression)
 * @method \Magelight\Dbal\Db\Common\Orm     orWhereGt($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     orWhereGte($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     orWhereLt($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     orWhereLte($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     orWhereLike($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     orWhereIn($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     orWhereNotIn($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     orWhereEx($expression, $params)
 *
 * @method \Magelight\Dbal\Db\Common\Orm     andWhereEq($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     andWhereNeq($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     andWhereNull($expression)
 * @method \Magelight\Dbal\Db\Common\Orm     andWhereNotNull($expression)
 * @method \Magelight\Dbal\Db\Common\Orm     andWhereGt($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     andWhereGte($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     andWhereLt($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     andWhereLte($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     andWhereLike($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     andWhereIn($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     andWhereNotIn($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     andWhereEx($expression, $params)
 *
 * @method \Magelight\Dbal\Db\Common\Orm     andNotWhereEq($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     andNotWhereNeq($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     andNotWhereNull($expression)
 * @method \Magelight\Dbal\Db\Common\Orm     andNotWhereNotNull($expression)
 * @method \Magelight\Dbal\Db\Common\Orm     andNotWhereGt($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     andNotWhereGte($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     andNotWhereLt($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     andNotWhereLte($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     andNotWhereLike($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     andNotWhereIn($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     andNotWhereNotIn($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     andNotWhereEx($expression, $params)
 *
 * @method \Magelight\Dbal\Db\Common\Orm     orNotWhereEq($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     orNotWhereNeq($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     orNotWhereNull($expression)
 * @method \Magelight\Dbal\Db\Common\Orm     orNotWhereNotNull($expression)
 * @method \Magelight\Dbal\Db\Common\Orm     orNotWhereGt($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     orNotWhereGte($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     orNotWhereLt($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     orNotWhereLte($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     orNotWhereLike($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     orNotWhereIn($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     orNotWhereNotIn($expression, $param)
 * @method \Magelight\Dbal\Db\Common\Orm     orNotWhereEx($expression, $params)
 */
abstract class Orm
{
    use \Magelight\Cache\Cache;

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
     * Get Orm class by type
     *
     * @param string $type
     * @return string
     */
    public static function getOrmClassByType($type)
    {
        return '\\Magelight\\Dbal\\Db\\' . ucfirst(strtolower($type)) . '\\Orm';
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
     * @param \Magelight\Dbal\Db\Common\Adapter $db
     */
    public function __construct(\Magelight\Dbal\Db\Common\Adapter $db)
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
}
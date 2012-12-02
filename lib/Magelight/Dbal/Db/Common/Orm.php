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
abstract class Orm
{
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
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

namespace Magelight;

/**
 * @method static \Magelight\Model forge($data = [], $forceNew = false)
 */
abstract class Model
{
    use \Magelight\Forgery;

    protected static $_dbIndex = \Magelight\App::DEFAULT_INDEX;

    protected static $_tableName = '';

    protected static $_idField = 'id';

    protected static $_defaultValues = [];

    /**
     * Orm instance
     *
     * @var \Magelight\Dbal\Db\Mysql\Orm
     */
    protected $_orm = null;

    /**
     * Forgery constructor
     *
     * @param array $data
     * @param bool $forceNew
     */
    public function __forge($data = [], $forceNew = false)
    {
        $this->setOrm(static::callStaticLate('orm'));
        if (!empty($data) && is_array($data)) {
            $this->_orm->create($data, $forceNew);
        }
    }

    /**
     * Set model orm
     *
     * @param Dbal\Db\Common\Orm $orm
     */
    public function setOrm(\Magelight\Dbal\Db\Common\Orm $orm)
    {
        $this->_orm = $orm;
    }

    /**
     * Get model orm
     *
     * @return Dbal\Db\Mysql\Orm
     */
    public static function orm()
    {
        $db = \Magelight::app()->db(static::callStaticLate('getDbIndex'));
        $ormClass = \Magelight\Dbal\Db\Common\Orm::getOrmClassByType($db->getType());

        $orm = new $ormClass($db);
        /*  @var $orm \Magelight\Dbal\Db\Mysql\Orm */
        $orm->setIdColumn(static::callStaticLate('getIdField'));
        $orm->setTableName(static::callStaticLate('getTableName'));
        $orm->setModelName(static::getClassRedefinition());
        return $orm;
    }

    public static function getDbIndex()
    {
        return static::$_dbIndex;
    }

    /**
     * Get model table name
     *
     * @return string
     */
    public static function getTableName()
    {
        return static::$_tableName;
    }

    /**
     * Get Model ID Field
     *
     * @return string
     */
    public static function getIdField()
    {
        return static::$_idField;
    }

    /**
     * Magic setter
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->_orm->setValue($name, $value);
    }

    /**
     * Magic getter
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->_orm->getValue($name);
    }

    /**
     * Delete model by id
     *
     * @param mixed $id
     *
     * @return int|null
     */
    public static function deleteById($id)
    {
        $orm = static::callStaticLate('orm');
        return $orm->delete($id);
    }

    /**
     * Save model
     *
     * @param bool $safeMode
     * @param bool $ignore - insert ignore
     * @param bool $onDuplicateKeyUpdate - on duplicate key update record
     * @return mixed
     */
    public function save($safeMode = false, $ignore = false, $onDuplicateKeyUpdate = false)
    {
        if ($this->_orm->isNew()) {
            $this->_orm->mergeData(static::$_defaultValues);
        }
        return $this->_orm->save($safeMode, $ignore, $onDuplicateKeyUpdate);
    }

    /**
     * Delete model instance by internal or given id
     *
     * @param mixed $id
     *
     * @return int|null
     */
    public function delete($id = null)
    {
        return $this->_orm->delete($id);
    }

    /**
     * Get model data as array
     *
     * @param array|string $fields
     *
     * @return array
     */
    public function asArray($fields = [])
    {
        $fields = !is_array($fields) ? func_get_args() : $fields;
        return $this->_orm->getData($fields);
    }

    /**
     * Find model instance by id
     *
     * @param mixed $id
     *
     * @return Model
     */
    public static function find($id)
    {
        return static::callStaticLate('findBy', [static::$_idField, $id]);
    }

    /**
     * Find model by field value
     *
     * @param $field
     * @param $value
     * @return Model
     */
    public static function findBy($field, $value)
    {
        $data = self::orm()->whereEq($field, $value)->fetchRow(true);
        if ($data === false) {
            return null;
        }
        return static::forge($data);
    }
}

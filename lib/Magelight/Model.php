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
    /**
     * Use forgery
     */
    use \Magelight\Forgery;

    /**
     * Database index in configuration
     *
     * @var string
     */
    protected static $_dbIndex = \Magelight\App::DEFAULT_INDEX;

    /**
     * Table name
     *
     * @var string
     */
    protected static $_tableName = '';

    /**
     * ID field name
     *
     * @var string
     */
    protected static $_idField = 'id';

    /**
     * Default model values
     *
     * @var array
     */
    protected static $_defaultValues = [];

    /**
     * Orm instance
     *
     * @var \Magelight\Db\Mysql\Orm
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
        $data = $this->_processDataBeforeCreate($data);

        $this->setOrm(static::callStaticLate('orm'));
        if (is_array($data)) {
            $this->_orm->create($data, $forceNew);
        }
    }

    /**
     * Process data before creating model
     *
     * @param array $data
     * @return array
     */
    protected function _processDataBeforeCreate($data)
    {
        return $data;
    }

    /**
     * After load handler
     *
     * @return Model
     */
    public function afterLoad()
    {
        return $this;
    }

    /**
     * Before save handler
     *
     * @return Model
     */
    protected function _beforeSave()
    {
        return $this;
    }

    /**
     * After Save handler
     *
     * @return Model
     */
    protected function _afterSave()
    {
        return $this;
    }

    /**
     * Set model ORM
     *
     * @param Db\Common\Orm $orm
     * @return Model
     */
    public function setOrm(\Magelight\Db\Common\Orm $orm)
    {
        $this->_orm = $orm;
        return $this;
    }

    /**
     * Get model orm
     *
     * @return Db\Mysql\Orm
     */
    public static function orm()
    {
        $db = \Magelight::app()->db(static::callStaticLate('getDbIndex'));
        $ormClass = \Magelight\Db\Common\Orm::getOrmClassByType($db->getType());

        $orm = new $ormClass($db);
        /*  @var $orm \Magelight\Db\Mysql\Orm */
        $orm->setIdColumn(static::callStaticLate('getIdField'));
        $orm->setTableName(static::callStaticLate('getTableName'));
        $orm->setModelName(static::getClassRedefinition());
        return $orm;
    }

    /**
     * Get database index
     *
     * @return string
     */
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
     * Isset magic
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->_orm->$name);
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
     * Getter magic
     *
     * @param string $name
     * @return mixed
     */
    public function &__get($name)
    {
        return $this->_orm->getValue($name);
    }

    /**
     * Unset magic
     *
     * @param string $name
     * @return Db\Common\Orm
     */
    public function __unset($name)
    {
        return $this->_orm->unsetValue($name);
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
        $this->_beforeSave();
        if ($this->_orm->isNew()) {
            $this->_orm->mergeData(static::$_defaultValues);
        }
        $result = $this->_orm->save($safeMode, $ignore, $onDuplicateKeyUpdate);
        $this->_afterSave();
        return $result;
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
     * Delete all models data from DB by field value
     *
     * @param string $field
     * @param mixed $value
     *
     * @return int|null
     */
    public function deleteBy($field, $value)
    {
        return $this->_orm->deleteBy($field, $value);
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
        return self::orm()->whereEq($field, $value)->fetchModel();
    }

    /**
     * Rip array of models to array representation (using model`s asArray() method)
     *
     * @param array $arrayOfModels
     * @param array $fields
     * @return array[\Magelight\Model]
     */
    public static function modelsToArrayRecursive($arrayOfModels = [], $fields = [])
    {
        $ret = [];
        foreach ($arrayOfModels as $item) {
            if ($item instanceof \Magelight\Model) {
                $ret[] = $item->asArray($fields);
            } elseif (is_array($item)) {
                $ret[] = self::modelsToArrayRecursive($item, $fields);
            } else {
                $ret[] = $item;
            }
        }
        return $ret;
    }
}

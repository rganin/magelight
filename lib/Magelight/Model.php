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

namespace Magelight;
use Magelight\Db\Common\Orm;

/**
 * @method static $this forge($data = [], $forceNew = false)
 * @method static $this getInstance()
 */
class Model
{
    /**
     * Use forgery
     */
    use \Magelight\Traits\TForgery;

    /**
     * Use coalesce trait
     */
    use Traits\TCoalesce;

    /**
     * Database index in configuration
     *
     * @var string
     */
    protected static $dbIndex = \Magelight\App::DEFAULT_INDEX;

    /**
     * Table name
     *
     * @var string
     */
    protected static $tableName = '';

    /**
     * ID field name
     *
     * @var string
     */
    protected static $idField = 'id';

    /**
     * Default model values
     *
     * @var array
     */
    protected static $defaultValues = [];

    /**
     * Orm instance
     *
     * @var \Magelight\Db\Mysql\Orm
     */
    protected $orm;

    /**
     * Forgery constructor
     *
     * @param array $data
     * @param bool $forceNew
     */
    public function __forge($data = [], $forceNew = false)
    {
        $data = $this->processDataBeforeCreate($data);

        $this->setOrm(static::callStaticLate('orm'));
        if (is_array($data)) {
            $this->orm->create($data, $forceNew);
        }
    }

    /**
     * Process data before creating model
     *
     * @param array $data
     * @return array
     */
    protected function processDataBeforeCreate($data)
    {
        return $data;
    }

    /**
     * After load handler
     *
     * @return $this
     */
    public function afterLoad()
    {
        return $this;
    }

    /**
     * Before save handler
     *
     * @return $this
     */
    protected function beforeSave()
    {
        return $this;
    }

    /**
     * After Save handler
     *
     * @return $this
     */
    protected function afterSave()
    {
        return $this;
    }

    /**
     * Set model ORM
     *
     * @param Db\Common\Orm $orm
     * @return $this
     */
    public function setOrm(\Magelight\Db\Common\Orm $orm)
    {
        $this->orm = $orm;
        return $this;
    }

    /**
     * Get model orm
     *
     * @return Db\Common\Orm
     */
    public static function orm()
    {
        $db = \Magelight\App::getInstance()->db(static::callStaticLate('getDbIndex'));
        $ormClass = \Magelight\Db\Common\Orm::getOrmClassByType($db->getType());

        $orm = call_user_func_array([$ormClass, 'forge'], [$db]);
        /*  @var $orm \Magelight\Db\Mysql\Orm */
        $orm->setIdColumn(static::callStaticLate('getIdField'));
        $orm->setTableName(static::callStaticLate('getTableName'));
        $orm->setModelName(static::getClassRedefinition());
        return $orm;
    }

    /**
     * @return Db\Common\Orm|null
     */
    public function getOrm()
    {
        if (!$this->orm instanceof Db\Mysql\Orm) {
            $this->setOrm(static::callStaticLate('orm'));
        }
        return $this->orm;
    }

    /**
     * Get database index
     *
     * @return string
     */
    public static function getDbIndex()
    {
        return static::$dbIndex;
    }

    /**
     * Get model table name
     *
     * @return string
     */
    public static function getTableName()
    {
        return static::$tableName;
    }

    /**
     * Get Model ID Field
     *
     * @return string
     */
    public static function getIdField()
    {
        return static::$idField;
    }

    /**
     * Get model ID
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->{self::getIdField()};
    }

    /**
     * Isset magic
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->orm->$name);
    }

    /**
     * Magic setter
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->orm->setValue($name, $value);
    }

    /**
     * Getter magic
     *
     * @param string $name
     * @return mixed
     */
    public function &__get($name)
    {
        return $this->orm->getValue($name);
    }

    /**
     * Unset magic
     *
     * @param string $name
     * @return Db\Common\Orm
     */
    public function __unset($name)
    {
        return $this->orm->unsetValue($name);
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
        $this->beforeSave();
        if ($this->orm->isNew()) {
            $this->orm->mergeData(static::$defaultValues);
        }
        $result = $this->orm->save($safeMode, $ignore, $onDuplicateKeyUpdate);
        $this->afterSave();
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
        return $this->orm->delete($id);
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
        return $this->orm->deleteBy($field, $value);
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
        return $this->orm->getData($fields);
    }

    /**
     * Find model instance by id
     *
     * @param mixed $id
     *
     * @return $this
     */
    public static function find($id)
    {
        return static::callStaticLate('findBy', [static::$idField, $id]);
    }

    /**
     * Find model by field value
     *
     * @param string $field
     * @param mixed $value
     * @return $this|null
     */
    public static function findBy($field, $value)
    {
        $orm = static::callStaticLate('orm');
        return $orm->whereEq($field, $value)->fetchModel();
    }

    /**
     * Find model by field value
     *
     * @param array $fieldValueArray - array of fields and values, like ['id' => '2', 'status' => 'open']
     * @return $this|null
     */
    public static function findByFields(array $fieldValueArray)
    {
        /** @var Orm $orm */
        $orm = static::callStaticLate('orm');
        foreach ($fieldValueArray as $field => $value) {
            $orm->whereEq($field, $value);
        }
        return $orm->fetchModel();
    }

    /**
     * Find model by expression
     *
     * @param Db\Common\Expression\Expression $expression
     * @return $this|null
     */
    public static function findByExpression(\Magelight\Db\Common\Expression\Expression $expression)
    {
        /** @var Orm $orm */
        $orm = static::callStaticLate('orm');
        return $orm->whereEx($expression)->fetchModel();
    }

    /**
     * Rip array of models to array representation (using model`s asArray() method)
     *
     * @param array $arrayOfModels
     * @param array $fields
     * @return array
     */
    public static function modelsToArrayRecursive($arrayOfModels = [], $fields = [])
    {
        $ret = [];
        foreach ($arrayOfModels as $item) {
            if ($item instanceof \Magelight\Model) {
                $ret[] = $item->asArray($fields);
            } else {
                $ret[] = $item;
            }
        }
        return $ret;
    }

    /**
     * Get models flat collection
     *
     * @return Db\Collection
     */
    public static function getFlatCollection()
    {
        return \Magelight\Db\Collection::forge(static::callStaticLate('orm'));
    }

    /**
     * Merge data with model data
     *
     * @param array $data
     * @param bool $overwrite
     * @return $this
     */
    public function mergeData($data = [], $overwrite = false)
    {
        $this->orm->mergeData($data, $overwrite);
        return $this;
    }

    /**
     * Properties cleanup HTML code
     *
     * @param array $propertiesNamesArray
     */
    public function escapePropertiesHtml($propertiesNamesArray = [])
    {
        foreach ($propertiesNamesArray as $property) {
            $this->$property = htmlspecialchars($this->$property);
        }
    }

    /**
     * Int typecast on properties
     *
     * @param array $propertiesNamesArray
     */
    public function castPropertiesInt($propertiesNamesArray = [])
    {
        foreach ($propertiesNamesArray as $property) {
            $this->$property = (int)$this->$property;
        }
    }

    /**
     * String typecast on properties
     *
     * @param array $propertiesNamesArray
     */
    public function castPropertiesString($propertiesNamesArray = [])
    {
        foreach ($propertiesNamesArray as $property) {
            $this->$property = (string)$this->$property;
        }
    }

    /**
     * Float typecast on properties
     *
     * @param array $propertiesNamesArray
     */
    public function castPropertiesFloat($propertiesNamesArray = [])
    {
        foreach ($propertiesNamesArray as $property) {
            $this->$property = floatval($this->$property);
        }
    }

    /**
     * Array typecast on properties
     *
     * @param array $propertiesNamesArray
     */
    public function castPropertiesArray($propertiesNamesArray = [])
    {
        foreach ($propertiesNamesArray as $property) {
            $this->$property = (array)$this->$property;
        }
    }

    /**
     * Object typecast on properties
     *
     * @param array $propertiesNamesArray
     */
    public function castPropertiesObject($propertiesNamesArray = [])
    {
        foreach ($propertiesNamesArray as $property) {
            $this->$property = (object)$this->$property;
        }
    }

    /**
     * Get random IDs set
     *
     * @param int $limit
     * @return array
     */
    public function getRandomIds($limit)
    {
        $return = [];
        $result = $this->getRandomIdsDataSource($limit)->fetchAll();
        if ($result) {
            foreach ($result as $resultRow) {
                $return[] = $resultRow[$this->getIdField()];
            }
        }
        return $return;
    }

    /**
     * Get random IDs data source
     *
     * @param int $limit
     * @return Db\Common\Orm
     */
    protected function getRandomIdsDataSource($limit)
    {
        return $this->orm()
            ->selectFields([$this->getIdField()])
            ->orderByDesc('RAND()')
            ->limit($limit);
    }
}

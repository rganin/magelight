<?php
/**
 * Created by JetBrains PhpStorm.
 * User: rganin
 * Date: 11.10.13
 * Time: 13:41
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Scaffold\Models;

/**
 * Class Scaffold
 * @package Magelight\Scaffold\Models
 *
 * @method static \Magelight\Scaffold\Models\Scaffold forge($connectionName = 'default')
 */
class Scaffold
{
    use \Magelight\Traits\TForgery;

    const DEFAULT_MODEL_CLASS = '\\Magelight\\Scaffold\\Models\\Entity';

    protected $_connectionName;

    protected static $_entities = [];

    /**
     * @var \Magelight\Db\Common\Adapter
     */
    protected $_db;



    /**
     * Forgery constructor
     *
     * @param string $connectionName
     */
    public function __forge($connectionName = 'default')
    {
        $this->setConnection($connectionName);
    }

    /**
     * Connection setter
     *
     * @param string $connectionName
     * @return $this
     */
    public function setConnection($connectionName = 'default')
    {
        $this->_connectionName = $connectionName;
        $this->_db = \Magelight::app()->db($connectionName);
        return $this;
    }

    /**
     * Fetch entities list
     *
     * @return array
     *
     */
    public function loadEntities()
    {
        $entitiesListConfig = \Magelight::app()->getConfig('global/scaffold/entities_list');
        $check = trim((string)$entitiesListConfig);
        if (empty($check)) {
            foreach ($this->_db->execute('SHOW TABLES;')->fetchAll() as $table) {
                static::$_entities[$table[0]] = [
                    'table_name'  => $table[0],
                    'entity'      => $table[0],
                    'comment'     => null,
                    'model_class' => null,
                    'id_field'    => 'id'
                ];
                static::$_entities['count']= $this->getEntityModel($table[0])->orm()->totalCount();
            }
        }

        /**
         * todo: implement scaffolding for user-defined tableset
         */
        return static::$_entities;
    }

    public function getEntities()
    {
        return static::$_entities;
    }

    public function getEntityModel($entity, $data = [], $forceNew = false)
    {
        $modelClass = isset(static::$_entities[$entity]['model_class'])
            ? static::$_entities[$entity]['model_class']
            : self::DEFAULT_MODEL_CLASS;

        /** @var $entityModel \Magelight\Scaffold\Models\Entity */
        $entityModel = static::callStaticLate([\Magelight::app()->getClassName($modelClass), 'forge'], [$data, $forceNew]);
        $entityModel->orm()->setTableName($this->_getEntityTable($entity));
        $entityModel->orm()->setIdColumn($this->_getEntityIdField($entity));
        $entityModel->orm()->setModelName($modelClass);

        return $entityModel;
    }

    protected function _getEntityTable($entity)
    {
        if (isset(static::$_entities[$entity]['table_name'])) {
            return static::$_entities[$entity]['table_name'];
        } else {
            throw new \Magelight\Exception("Entity `{$entity}` table is not defined!");
        }
    }

    protected function _getEntityIdField($entity)
    {
        if (isset(static::$_entities[$entity]['id_field'])) {
            return static::$_entities[$entity]['id_field'];
        } else {
            throw new \Magelight\Exception("Entity `{$entity}` id field is not defined!");
        }
    }
}
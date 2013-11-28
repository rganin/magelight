<?php
/**
 * Created by JetBrains PhpStorm.
 * User: rganin
 * Date: 11.10.13
 * Time: 13:41
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Admin\Models\Scaffold;

/**
 * Class Scaffold
 * @package Magelight\Scaffold\Models
 *
 * @method static \Magelight\Admin\Models\Scaffold\Scaffold forge($connectionName = 'default')
 */
class Scaffold
{
    use \Magelight\Traits\TForgery;

    const DEFAULT_MODEL_CLASS = '\\Magelight\\Admin\\Models\\Scaffold\\Entity';

    protected $_connectionName;

    protected static $_entities = [];

    /**
     * @var \Magelight\Db\Common\Adapter
     */
    protected $_db;

    /**
     * @var \SimpleXMLElement
     */
    protected $_entitiesConfig;

    /**
     * @var \SimpleXMLElement
     */
    protected $_defaultEntityConfig;

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

    public function getEntitiesConfig()
    {
        if (empty($this->_entitiesConfig)) {
            $entitiesConfig = \Magelight::app()->getConfig('admin/scaffold/entities');
            $this->_defaultEntityConfig = clone $entitiesConfig->default;
            unset($entitiesConfig->default);
            foreach ($entitiesConfig->children() as $child) {
                /** @var $child \SimpleXMLElement */
                /** @var $draft \SimpleXMLElement */
                /** @var $fieldDraft \SimpleXMLElement */

                $draft = clone $this->_defaultEntityConfig;
                \Magelight\Components\Loaders\Config::mergeConfig($draft, $child);
                \Magelight\Components\Loaders\Config::mergeConfig($entitiesConfig->{$child->getName()}, $draft);

                $defaultFieldConfig = clone $entitiesConfig->{$child->getName()}->fields->default;
                unset ($entitiesConfig->{$child->getName()}->fields->default);
                foreach ($entitiesConfig->{$child->getName()}->fields->children() as $field) {
                    $fieldDraft = clone $defaultFieldConfig;
                    \Magelight\Components\Loaders\Config::mergeConfig($fieldDraft, $field);
                    \Magelight\Components\Loaders\Config::mergeConfig($entitiesConfig->{$child->getName()}->fields->{$field->getName()}, $fieldDraft);
                }
            }
            $this->_entitiesConfig = $entitiesConfig;
        }
        return $this->_entitiesConfig;
    }

    /**
     * Get entity configuration by table name
     *
     * @param $tableName
     * @return \SimpleXMLElement|\SimpleXMLElement[]
     */
    public function getEntityConfigByTableName($tableName)
    {
        foreach ($this->_entitiesConfig->children() as $child) {
            if (!empty($child->table_name)) {
                return $this->_entitiesConfig->{$child->getName()};
            }
        }
        return $this->_defaultEntityConfig;
    }

    /**
     * Get defined entity fields
     *
     * @param string $entity
     * @return array
     */
    public function getEntityFields($entity)
    {
        $fields = [static::$_entities[$entity]['id_field']];
        if (!empty($this->_entitiesConfig->$entity->fields)) {
            foreach ($this->_entitiesConfig->$entity->fields->children() as $field) {
                $fields[] = $field->getName();
            }
            return $fields;
        } else {
            return $this->getEntityModel($entity)->getOrm()->getTableFields();
        }
    }

    /**
     * Get entity field configuration
     *
     * @param string $entity
     * @param string $field
     * @return \SimpleXMLElement
     */
    public function getEntityFieldConfig($entity, $field)
    {
        if (!empty($this->_entitiesConfig->$entity->fields->$field)) {
            return $this->_entitiesConfig->$entity->fields->$field;
        } else {
            return $this->_defaultEntityConfig->fields->default;
        }
    }

    /**
     * Fetch entities list
     *
     * @return array
     *
     */
    public function loadEntities()
    {
        if (empty(self::$_entities)) {
            $configEntities = $this->getEntitiesConfig()->children();

            if (!empty($configEntities)) {
                foreach ($configEntities as $entity) {
                    /** @var $entity \SimpleXMLElement */
                    static::$_entities[$entity->getName()] = [
                        'entity'      => $entity->getName(),
                        'comment'     => (string)$entity->comment,
                        'model_class' => !empty($entity->model_class) ? (string)$entity->model_class : self::DEFAULT_MODEL_CLASS,
                    ];

                    static::$_entities[$entity->getName()]['table_name'] = !empty($entity->table_name)
                            ? (string)$entity->table_name
                            : (!empty(static::$_entities[$entity->getName()]['model_class'])
                                ? static::callStaticLate([\Magelight::app()->getClassName(static::$_entities[$entity->getName()]['model_class']), 'getTableName'])
                                : $entity->getName()
                            );
                    static::$_entities[$entity->getName()]['id_field'] = !empty($entity->id_field)
                        ? (string)$entity->id_field
                        : (!empty(static::$_entities[$entity->getName()]['model_class'])
                            ? static::callStaticLate([\Magelight::app()->getClassName(static::$_entities[$entity->getName()]['model_class']), 'getIdField'])
                            : 'id'
                        );
                    static::$_entities[$entity->getName()]['count']= $this->getEntityModel($entity->getName())->getOrm()->totalCount();
                }
            } else {
                foreach ($this->_db->execute('SHOW TABLES;')->fetchAll() as $table) {
                    static::$_entities[$table[0]] = [
                        'table_name'  => $table[0],
                        'entity'      => $table[0],
                        'comment'     => null,
                        'model_class' => null,
                        'id_field'    => 'id'
                    ];
                    static::$_entities[$table[0]]['count']= $this->getEntityModel($table[0])->getOrm()->totalCount();
                }
            }
        }
        return static::$_entities;
    }

    public function getEntities()
    {
        return static::$_entities;
    }

    public function getEntityModelClass($entity) {
        return isset(static::$_entities[$entity]['model_class'])
            ? static::$_entities[$entity]['model_class']
            : self::DEFAULT_MODEL_CLASS;
    }

    public function getEntityModel($entity, $data = [], $forceNew = false)
    {
        $modelClass = $this->getEntityModelClass($entity);
        /** @var $entityModel \Magelight\Admin\Models\Scaffold\Entity */
        $entityModel = static::callStaticLate([\Magelight::app()->getClassName($modelClass), 'forge'], [$data, $forceNew]);
        if ($modelClass === self::DEFAULT_MODEL_CLASS) {
            $entityModel->getOrm()->setTableName($this->_getEntityTable($entity));
            $entityModel->getOrm()->setIdColumn($this->_getEntityIdField($entity));
            $entityModel->getOrm()->setModelName($modelClass);
        }
        return $entityModel;
    }

    public function loadEntityModel($entity, $id)
    {
        return static::callStaticLate([\Magelight::app()->getClassName($this->getEntityModelClass($entity)), 'find'], [$id]);
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
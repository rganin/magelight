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
 * @method static \Magelight\Admin\Models\Scaffold\Scaffold getInstance($connectionName = 'default')
 */
class Scaffold
{
    /**
     * Using forgery
     */
    use \Magelight\Traits\TForgery;

    /**
     * Default model class
     */
    const DEFAULT_MODEL_CLASS = "\\Magelight\\Admin\\Models\\Scaffold\\Entity";

    /**
     * Connection name
     *
     * @var string
     */
    protected $_connectionName;

    /**
     * Entities configuration array
     *
     * @var array
     */
    protected $_entities = [];

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
     *
     * @return $this
     */
    public function setConnection($connectionName = 'default')
    {
        $this->_connectionName = $connectionName;
        $this->_db = \Magelight::app()->db($connectionName);
        return $this;
    }

    /**
     * Get entities configuration
     *
     * @return mixed|\SimpleXMLElement
     */
    public function getEntitiesConfig()
    {
        if (empty($this->_entitiesConfig)) {
            $entitiesConfig = clone \Magelight::app()->getConfig('admin/scaffold/entities');
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
                    \Magelight\Components\Loaders\Config::mergeConfig(
                        $entitiesConfig->{$child->getName()}->fields->{$field->getName()},
                        $fieldDraft
                    );
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
            if (!empty($child->table_name) && (string)$child->table_name == $tableName) {
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
        $fields = [$this->_entities[$entity]['id_field']];
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
        if (empty($this->_entities)) {
            $configEntities = $this->getEntitiesConfig()->children();

            if (!empty($configEntities)) {
                foreach ($configEntities as $entity) {
                    /** @var $entity \SimpleXMLElement */
                    $this->_entities[$entity->getName()] = [
                        'entity'      => $entity->getName(),
                        'comment'     => (string)$entity->comment,
                        'model_class' => !empty($entity->model_class)
                            ? (string)$entity->model_class
                            : self::DEFAULT_MODEL_CLASS,
                    ];
                    $this->_defineEntityIdFieldName($entity)->_defineEntityTableName($entity);
                    $this->_entities[$entity->getName()]['count']= $this->getEntityModel($entity->getName())
                        ->getOrm()
                        ->totalCount();
                }
            } else {
                foreach ($this->_db->execute('SHOW TABLES;')->fetchAll() as $table) {
                    $this->_entities[$table[0]] = [
                        'table_name'  => $table[0],
                        'entity'      => $table[0],
                        'comment'     => null,
                        'model_class' => null,
                        'id_field'    => 'id'
                    ];
                    $this->_entities[$table[0]]['count']= $this->getEntityModel($table[0])->getOrm()->totalCount();
                }
            }
        }
        return $this->_entities;
    }

    /**
     * Define entity table name
     *
     * @param \SimpleXMLElement $entity
     * @return $this
     */
    protected function _defineEntityTableName(\SimpleXMLElement $entity)
    {
        if (!empty($entity->table_name)) {
            $this->_setEntityTable($entity->getName(), (string)$entity->table_name);
        } else {
            if ((string)$entity->model_class !== self::DEFAULT_MODEL_CLASS) {
                $this->_setEntityTable(
                    $entity->getName(),
                    static::callStaticLate([
                        \Magelight::app()->getClassName(
                            $this->_entities[$entity->getName()]['model_class']
                        ),
                        'getTableName'
                    ])
                );
            } else {
                $this->_setEntityTable($entity->getName(), $entity->getName());
            }
        }
        return $this;
    }

    /**
     * Define entity ID Field name
     *
     * @param \SimpleXMLElement $entity
     * @return $this
     */
    protected function _defineEntityIdFieldName(\SimpleXMLElement $entity)
    {
        if (!empty($entity->id_field)) {
            $this->_setEntityIdField($entity->getName(), (string)$entity->id_field);
        } else {
            if ((string)$entity->model_class !== self::DEFAULT_MODEL_CLASS) {
                $this->_setEntityIdField(
                    $entity->getName(),
                    static::callStaticLate([
                        \Magelight::app()->getClassName(
                            $this->_entities[$entity->getName()]['model_class']
                        ),
                        'getIdField'
                    ])
                );
            } else {
                $this->_setEntityIdField($entity->getName(), 'id');
            }
        }
        return $this;
    }

    /**
     * Set entity table name
     *
     * @param string $entity
     * @param string $table
     */
    protected function _setEntityTable($entity, $table)
    {
        $this->_entities[$entity]['table_name'] = $table;
    }

    /**
     * Set entity ID Field name
     *
     * @param string $entity
     * @param string $idFieldName
     */
    protected function _setEntityIdField($entity, $idFieldName)
    {
        $this->_entities[$entity]['id_field'] = $idFieldName;
    }

    /**
     * Get entity id field
     *
     * @param string $entityName
     * @return mixed
     */
    public function getEntityIdField($entityName)
    {
        return $this->_entities[$entityName]['id_field'];
    }

    /**
     * Get entity table name
     *
     * @param string $entityName
     * @return mixed
     */
    public function getEntityTableName($entityName)
    {
        return $this->_entities[$entityName]['id_field'];
    }

    /**
     * Get entities config array
     *
     * @return array
     */
    public function getEntities()
    {
        return $this->_entities;
    }

    /**
     * Get entity model class
     *
     * @param $entity
     * @return string
     */
    public function getEntityModelClass($entity) {
        return isset($this->_entities[$entity]['model_class'])
            ? $this->_entities[$entity]['model_class']
            : self::DEFAULT_MODEL_CLASS;
    }

    /**
     * Get entity model
     *
     * @param string $entity
     * @param array $data
     * @param bool $forceNew
     * @return Entity
     */
    public function getEntityModel($entity, $data = [], $forceNew = false)
    {
        $modelClass = $this->getEntityModelClass($entity);
        /** @var $entityModel \Magelight\Admin\Models\Scaffold\Entity */
        $entityModel = static::callStaticLate(
            [\Magelight::app()->getClassName($modelClass), 'forge'], [$data, $forceNew]
        );
        if ($modelClass === self::DEFAULT_MODEL_CLASS) {
            $entityModel->getOrm()->setTableName($this->_getEntityTable($entity));
            $entityModel->getOrm()->setIdColumn($this->_getEntityIdField($entity));
            $entityModel->getOrm()->setModelName($modelClass);
        }
        return $entityModel;
    }

    /**
     * Load entity model
     *
     * @param string $entity
     * @param int $id
     * @return mixed
     */
    public function loadEntityModel($entity, $id)
    {
        return static::callStaticLate([\Magelight::app()->getClassName(
            $this->getEntityModelClass($entity)), 'find'], [$id]
        );
    }

    /**
     * Get entity table
     *
     * @param string $entity
     * @return string
     * @throws \Magelight\Exception
     */
    protected function _getEntityTable($entity)
    {
        if (isset($this->_entities[$entity]['table_name'])) {
            return $this->_entities[$entity]['table_name'];
        } else {
            throw new \Magelight\Exception("Entity `{$entity}` table is not defined!");
        }
    }

    /**
     * Get entity ID field name
     *
     * @param $entity
     * @return string
     * @throws \Magelight\Exception
     */
    protected function _getEntityIdField($entity)
    {
        if (isset($this->_entities[$entity]['id_field'])) {
            return $this->_entities[$entity]['id_field'];
        } else {
            throw new \Magelight\Exception("Entity `{$entity}` id field is not defined!");
        }
    }
}

<?php
/**
 * Created by JetBrains PhpStorm.
 * User: rganin
 * Date: 11.10.13
 * Time: 13:44
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Admin\Blocks\Scaffold;

/**
 * Class EntityForm
 * @package Magelight\Admin\Blocks
 *
 * @method static \Magelight\Admin\Blocks\Scaffold\EntityForm forge($entity, $id = null)
 */
class EntityForm extends \Magelight\Webform\Blocks\Form
{
    const URL_PATTERN = 'admin/scaffold/{entity}/update';

    protected $_entity;

    /**
     * @var \Magelight\Admin\Models\Scaffold\Scaffold
     */
    protected $_scaffold;

    /**
     * @var \Magelight\Model
     */
    protected $_model;

    /**
     * Forgery constructor
     *
     * @param string $entity
     */
    public function __forge($entity)
    {
        $this->_entity = $entity;
        $this->_scaffold = \Magelight\Admin\Models\Scaffold\Scaffold::forge();
        $this->_scaffold->loadEntities();

        $this->setConfigs('scaffold-' . $entity, $this->url(self::URL_PATTERN, ['entity' => $entity]));

        $this->setHorizontal();

        $fieldSet = \Magelight\Webform\Blocks\FieldSet::forge();

        $this->_model = $this->_scaffold->getEntityModel($this->_entity);


        foreach ($this->_scaffold->getEntityFields($this->_entity) as $field) {
            $fieldConfig = $this->_scaffold->getEntityFieldConfig($this->_entity, $field);
            $row = \Magelight\Webform\Blocks\Row::forge();
            /** @var $fieldInput  \Magelight\Webform\Blocks\Elements\Input */
            $fields = [];
            $fieldInput = self::callStaticLate([(string)$fieldConfig->class, 'forge']);
            $fieldInput->setName($field);
            $label = !empty($fieldConfig->label) ? (string)$fieldConfig->label : $field;
            $hint = !empty($fieldConfig->hint) ? (string)$fieldConfig->hint : '';
            $fields[] = $fieldInput;
            if ((bool)(string)$fieldConfig->allow_null) {
                $checkboxNull = \Magelight\Webform\Blocks\Elements\LabeledCheckbox::forge();
                $checkboxNull->setName("set-null[$field]");
                $checkboxNull->setContent('Set NULL');
                $fields[] = $checkboxNull;
            }
            $row->addField($fields, $label, $hint);
            $fieldSet->addRow($row);
        }
        $this->addFieldset($fieldSet);
        $this->createResultRow(true);
        $this->addButtonsRow([\Magelight\Webform\Blocks\Elements\Button::forge()->setType('submit')->setContent('Save')]);
    }

    /**
     * Get scaffolding object
     *
     * @return \Magelight\Admin\Models\Scaffold\Scaffold
     */
    public function getScaffold()
    {
        return $this->_scaffold;
    }

    /**
     * Load entity data by ID
     *
     * @param int|null $id
     * @return $this
     */
    public function loadEntityData($id = null)
    {
        if (is_null($id)) {
            return $this;
        }
        $this->_model = $this->_model->getOrm()->whereEq($this->_model->getIdField(), $id)->fetchModel();
        if (empty($this->_model)) {
            return $this;
        }
        $this->setFormValues($this->_model->asArray());
        foreach ($this->_scaffold->getEntityFields($this->_entity) as $field) {
            $fieldConfig = $this->_scaffold->getEntityFieldConfig($this->_entity, $field);
            if ((bool)(string)$fieldConfig->allow_null) {
                if (is_null($this->_model->$field)) {
                    $checkboxNull = $this->getElementByName("set-null[$field]");
                    if ($checkboxNull instanceof \Magelight\Webform\Blocks\Elements\Checkbox) {
                        $checkboxNull->setChecked();
                    }
                }
            }
        }
        return $this;
    }
}

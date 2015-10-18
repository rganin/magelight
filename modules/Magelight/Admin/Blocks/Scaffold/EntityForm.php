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
 * @copyright Copyright (c) 2013 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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

    /**
     * @var string
     */
    protected $entity;

    /**
     * @var \Magelight\Admin\Models\Scaffold\Scaffold
     */
    protected $scaffold;

    /**
     * @var \Magelight\Model
     */
    protected $model;

    /**
     * Forgery constructor
     *
     * @param string $entity
     */
    public function __forge($entity)
    {
        $this->entity = $entity;
        $this->scaffold = \Magelight\Admin\Models\Scaffold\Scaffold::forge();
        $this->scaffold->loadEntities();

        $this->setConfigs('scaffold-' . $entity, $this->url(self::URL_PATTERN, ['entity' => $entity]));

        $this->setHorizontal();

        $fieldSet = \Magelight\Webform\Blocks\FieldSet::forge();

        $this->model = $this->scaffold->getEntityModel($this->entity);


        foreach ($this->scaffold->getEntityFields($this->entity) as $field) {
            $fieldConfig = $this->scaffold->getEntityFieldConfig($this->entity, $field);
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
        return $this->scaffold;
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
        $this->model = $this->model->getOrm()->whereEq($this->model->getIdField(), $id)->fetchModel();
        if (empty($this->model)) {
            return $this;
        }
        $this->setFormValues($this->model->asArray());
        foreach ($this->scaffold->getEntityFields($this->entity) as $field) {
            $fieldConfig = $this->scaffold->getEntityFieldConfig($this->entity, $field);
            if ((bool)(string)$fieldConfig->allow_null) {
                if (is_null($this->model->$field)) {
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

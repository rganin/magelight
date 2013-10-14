<?php
/**
 * Created by JetBrains PhpStorm.
 * User: rganin
 * Date: 11.10.13
 * Time: 13:44
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Scaffold\Blocks;

/**
 * Class EntityForm
 * @package Magelight\Scaffold\Blocks
 *
 * @method static EntityForm forge($entity, $id)
 */
class EntityForm extends \Magelight\Webform\Blocks\Form
{
    const URL_PATTERN = '/scaffold/{entity}/update/{id}';

    protected $_entity;

    protected $_id;

    protected $_scaffold;

    public function __forge($entity, $id)
    {
        $this->_entity = $entity;
        $this->_id = $id;
        $this->_scaffold = \Magelight\Scaffold\Models\Scaffold::forge();
        $this->_scaffold->loadEntities();
        $this->setConfigs('scaffold-' .$entity, $this->url(self::URL_PATTERN, ['entity' => $entity, 'id' => $id]));
        $this->setHorizontal();
        $fieldSet = \Magelight\Webform\Blocks\FieldSet::forge();
        $model = $this->_scaffold->getEntityModel($entity);
        $model = $model->getOrm()->whereEq($model->getIdField(), $id)->fetchModel();
        foreach ($this->_scaffold->getEntityFields($entity) as $field) {
            $fieldConfig = $this->_scaffold->getEntityFieldConfig($entity, $field);
            $row = \Magelight\Webform\Blocks\Row::forge();
            /** @var $field  \Magelight\Webform\Blocks\Elements\Input */
            $fields = [];
            $fieldInput = self::callStaticLate([(string)$fieldConfig->class, 'forge']);
            $fieldInput->setName($field);
            $fieldInput->setValue($model->$field);
            $label = !empty($fieldConfig->label) ? (string)$fieldConfig->label : $field;
            $hint = !empty($fieldConfig->hint) ? (string)$fieldConfig->hint : '';
            $fields[] = $fieldInput;
            if ((bool)(string)$fieldConfig->allow_null) {
                $checkboxNull = \Magelight\Webform\Blocks\Elements\LabeledCheckbox::forge();
                $checkboxNull->setName("set-null[$field]");
                $checkboxNull->setContent('Set NULL');
                if (is_null($model->$field)) {
                    $checkboxNull->setChecked();
                }
                $fields[] = $checkboxNull;
            }
            $row->addField($fields, $label, $hint);
            $fieldSet->addRow($row);
        }
        $this->addFieldset($fieldSet);
        $this->addButtonsRow([\Magelight\Webform\Blocks\Elements\Button::forge()->setType('submit')->setContent('Save')]);
    }

    public function beforeToHtml()
    {

    }
}

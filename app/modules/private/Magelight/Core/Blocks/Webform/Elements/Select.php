<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 18.11.12
 * Time: 13:41
 * To change this template use File | Settings | File Templates.
 */
namespace Magelight\Core\Blocks\Webform\Elements;
/**
 * @method static \Magelight\Core\Blocks\Webform\Elements\Select forge()
 */
class Select extends Abstraction\Field
{
    protected $_tag = 'select';

    /**
     * @return Select
     */
    public function setMulti()
    {
        return $this->setAttribute('multiple', 'multiple');
    }

    /**
     * @param SelectOption $option
     * @return Select
     */
    public function addOption(SelectOption $option)
    {
        return $this->addContent($option);
    }

    /**
     * @param SelectOption $optionGroup
     * @return Select
     */
    public function addGroup(SelectOptionGroup $optionGroup)
    {
        return $this->addContent($optionGroup);
    }

    /**
     * Import options from array
     *
     * @param array $options
     *
     * @return SelectOptionGroup
     */
    public function importOptions(array $options = [])
    {
        foreach ($options as $option)
        {
            if ( isset($option['options']) && is_array($option['options'])) {
                $label = isset($option['label']) ? $option['label'] : null;
                $groupOptions = !empty($option['options']) ? $option['options'] : [];
                $this->addGroup(SelectOptionGroup::forge()->setTitle($label)->importOptions($groupOptions));
            } else {
                $value = isset($option['value']) ? $option['value'] : null;
                $title = isset($option['title']) ? $option['title'] : $option['value'];
                $selected = isset($option['selected']) ? true : false;
                $this->addOption(SelectOption::forge()->setOptionParams($value, $title, $selected));
            }
        }
        return $this;
    }

}

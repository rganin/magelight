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

namespace Magelight\Core\Blocks\Webform\Elements;
/**
 * @method static \Magelight\Core\Blocks\Webform\Elements\Select forge()
 */
class Select extends Abstraction\Field
{
    /**
     * Element tag
     *
     * @var string
     */
    protected $_tag = 'select';

    /**
     * Set multi-select
     *
     * @return Select
     */
    public function setMulti()
    {
        return $this->setAttribute('multiple', 'multiple');
    }

    /**
     * Add option
     *
     * @param SelectOption $option
     * @return Select
     */
    public function addOption(SelectOption $option)
    {
        return $this->addContent($option);
    }

    /**
     * Add options group to element
     *
     * @param SelectOptionGroup $optionGroup
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
     * @return Select
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

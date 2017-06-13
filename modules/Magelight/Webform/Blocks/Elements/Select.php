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

namespace Magelight\Webform\Blocks\Elements;
/**
 * @method static $this forge()
 */
class Select extends Abstraction\Field
{
    /**
     * Element tag
     *
     * @var string
     */
    protected $tag = 'select';

    /**
     * @var SelectOption[]|SelectOptionGroup[]
     */
    protected $options = [];

    /**
     * Forgery
     */
    public function __forge()
    {
        $this->addClass('form-control');
    }

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
     * @return $this
     */
    public function addOption(SelectOption $option)
    {
        $this->options[] = $option;
        return $this->addContent($option);
    }

    /**
     * Add options group to element
     *
     * @param SelectOptionGroup $optionGroup
     * @return $this
     */
    public function addGroup(SelectOptionGroup $optionGroup)
    {
        $this->options[] = $optionGroup;
        return $this->addContent($optionGroup);
    }

    /**
     * Import options from array
     *
     * @param array $options
     *
     * @return $this
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

    /**
     * Set select value selected
     *
     * @param string $value
     * @return $this
     * @throws \Magelight\Exception
     */
    public function setValue($value)
    {
        foreach ($this->options as $option) {
            if ($option instanceof SelectOptionGroup) {
                $option->setValue($value);
                break;
            }
            if ($option->getValue() == $value) {
                $option->setAttribute('selected', 'selected');
                break;
            }
        }
        return $this;
    }
}

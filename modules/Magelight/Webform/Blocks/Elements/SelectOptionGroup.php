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
 * @method static \Magelight\Webform\Blocks\Elements\SelectOptionGroup forge()
 */
class SelectOptionGroup extends Abstraction\Field
{
    /**
     * Element tag
     *
     * @var string
     */
    protected $tag = 'optgroup';

    /**
     * @var SelectOption[]
     */
    protected $options = [];

    /**
     * Set option group title
     *
     * @param null $title
     * @return SelectOptionGroup
     */
    public function setTitle($title = null)
    {
        return $this->setAttribute('label', $title);
    }

    /**
     * Add option to group
     *
     * @param SelectOption $option
     * @return SelectOptionGroup
     */
    public function addOption(SelectOption $option)
    {
        $this->options[] = $option;
        return $this->addContent($option);
    }

    /**
     * Import options from array
     *
     * @param array $options
     * @return SelectOptionGroup
     */
    public function importOptions(array $options = [])
    {
        foreach ($options as $option)
        {
            $value = isset($option['value']) ? $option['value'] : null;
            $title = isset($option['title']) ? $option['title'] : $option['value'];
            $selected = isset($option['selected']) ? true : false;
            $this->addOption(SelectOption::forge()->setOptionParams($value, $title, $selected));
        }
        return $this;
    }

    /**
     * Set option group options value selected
     *
     * @param string $value
     * @return $this
     * @throws \Magelight\Exception
     */
    public function setValue($value)
    {
        foreach ($this->options as $option) {
            if ($option->getValue() == $value) {
                $option->setAttribute('selected', 'selected');
                break;
            }
        }
        return $this;
    }
}

<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 18.11.12
 * Time: 14:21
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Core\Blocks\Webform\Elements;
/**
 * @method static \Magelight\Core\Blocks\Webform\Elements\SelectOptionGroup forge()
 */
class SelectOptionGroup extends Abstraction\Field
{
    protected $_tag = 'optgroup';

    /**
     * @param null $title
     * @return SelectOptionGroup
     */
    public function setTitle($title = null)
    {
        return $this->setAttribute('label', $title);
    }

    /**
     * @param SelectOption $option
     * @return SelectOptionGroup
     */
    public function addOption(SelectOption $option)
    {
        return $this->addContent($option);
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
            $value = isset($option['value']) ? $option['value'] : null;
            $title = isset($option['title']) ? $option['title'] : $option['value'];
            $selected = isset($option['selected']) ? true : false;
            $this->addOption(SelectOption::forge()->setOptionParams($value, $title, $selected));
        }
        return $this;
    }
}

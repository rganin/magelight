<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 18.11.12
 * Time: 13:42
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Core\Blocks\Webform\Elements;
/**
 * @method static \Magelight\Core\Blocks\Webform\Elements\SelectOption forge()
 */
class SelectOption extends Abstraction\Field
{
    protected $_tag = 'option';

    public function setOptionParams($value, $title = null, $selected = null)
    {
        $this->setAttribute('value', $value);
        if ($selected) {
            $this->setAttribute('selected', 'selected');
        }
        if (!is_null($title)) {
            $this->setContent($title);
        }
        return $this;
    }
}
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
 * @method static \Magelight\Webform\Blocks\Elements\SelectOption forge()
 */
class SelectOption extends Abstraction\Field
{
    /**
     * Element tag
     *
     * @var string
     */
    protected $tag = 'option';

    /**
     * Set option attributes and content
     *
     * @param string $value
     * @param string $title
     * @param bool $selected
     * @return SelectOption
     */
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

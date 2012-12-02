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

namespace Magelight\Webform\Blocks;

/**
 * @method static \Magelight\Webform\Blocks\Row forge()
 */
class Row extends Elements\Abstraction\Element
{
    /**
     * Tag name
     *
     * @var string
     */
    protected $_tag = 'div';

    /**
     * Forgery constructor
     */
    public function __forge()
    {
        $this->addClass('control-group');
    }

    /**
     * Add field to row
     *
     * @param array|Elements\Abstraction\Field $field
     * @param string $label
     * @param string $hint
     * @return Row
     * @throws \Magelight\Exception
     */
    public function addField($field, $label = null, $hint = null)
    {
        if (!is_array($field)) {
            $field = [$field];
        }
        if (!empty($label)) {
            /* @var  Elements\Abstraction\Field $field[0]*/
            $this->addContent(Elements\Label::forge()->setFor($field[0]->getId())->setContent($label));
        }
        $controls =  Elements\Abstraction\Element::forge()->addClass('controls');

        foreach ($field as $fieldElement) {
            if (is_string($fieldElement) || $fieldElement instanceof \Magelight\Block) {
                /* @var $fieldElement Elements\Abstraction\Field*/
                $controls->addContent($fieldElement);
            } else {
                throw new \Magelight\Exception('FieldElement must be a string or instance of \\Magelight\\Blocks');
            }
        }

        if (!empty($hint)) {
            $controls->addContent(Elements\Hint::forge()->setContent($hint));
        }
        $this->addContent($controls);
        return $this;
    }
}

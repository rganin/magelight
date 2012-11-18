<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 18.11.12
 * Time: 2:23
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Core\Blocks\Webform;

/**
 * @method static \Magelight\Core\Blocks\Webform\Row forge()
 */
class Row extends Elements\Abstraction\Element
{
    /**
     * Tag name
     *
     * @var string
     */
    protected $_tag = 'div';

    public function __forge()
    {
        $this->addClass('control-group');
    }

    /**
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
                $controls->addContent($fieldElement);
            } else {
                throw new \Magelight\Exception('FieldElement must be a string or instance of \\Magelight\\Block');
            }
        }

        if (!empty($hint)) {
            $controls->addContent(Elements\Hint::forge()->setContent($hint));
        }
        $this->addContent($controls);
        return $this;
    }
}
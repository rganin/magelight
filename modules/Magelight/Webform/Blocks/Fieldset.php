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

namespace Magelight\Webform\Blocks;

/**
 * @method static \Magelight\Webform\Blocks\Fieldset forge()
 */
class Fieldset extends Row
{
    /**
     * Tag name
     *
     * @var string
     */
    protected $tag = 'fieldset';

    /**
     * Add row to fieldset
     *
     * @param Row $row
     * @return Fieldset
     */
    public function addRow(Row $row)
    {
        return $this->addContent($row);
    }

    /**
     * Add wield wrapped with row to fieldset
     *
     * @param Elements\Abstraction\Element|array $field
     * @param string $label
     * @param string $hint
     * @return Fieldset
     */
    public function addRowField($field, $label = null, $hint = null)
    {
        return $this->addRow(Row::forge()->addField($field, $label, $hint));
    }

    /**
     * Set fieldset legend
     *
     * @param string $legendText
     * @return Fieldset
     */
    public function setLegend($legendText)
    {
        array_unshift($this->content, Elements\Legend::forge()->setContent($legendText));
        return $this;
    }

    /**
     * Add input to fieldset
     *
     * @param string $type
     * @param string $name
     * @param string $label
     * @param string $hint
     * @return Fieldset
     */
    public function addInput($type, $name, $label = null, $hint = null)
    {
        return $this->addRowField(Elements\Input::forge()->setType($type)->setName($name), $label, $hint);
    }

    /**
     * Call magic (not fully implemented yet)
     *
     * @param string $name
     * @param array $arguments
     */
    public function __call($name, $arguments)
    {

    }
}

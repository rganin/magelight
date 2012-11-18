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

namespace Magelight\Core\Blocks\Webform;

/**
 * @method static \Magelight\Core\Blocks\Webform\Fieldset forge()
 */
class Fieldset extends Row
{
    /**
     * Tag name
     *
     * @var string
     */
    protected $_tag = 'fieldset';

    /**
     * @param Row $row
     * @return Fieldset
     */
    public function addRow(Row $row)
    {
        return $this->addContent($row);
    }

    public function addRowField($field, $label = null, $hint = null)
    {
        return $this->addRow(Row::forge()->addField($field, $label, $hint));
    }

    /**
     * @param $legendText
     * @return Fieldset
     */
    public function setLegend($legendText)
    {
        array_unshift($this->_content, Elements\Legend::forge()->setContent($legendText));
        return $this;
    }

    public function addInput($type, $name, $label = null, $hint = null)
    {
        return $this->addRowField(Elements\Input::forge()->setType($type)->setName($name), $label, $hint);
    }



    public function __call($name, $arguments)
    {

    }
}

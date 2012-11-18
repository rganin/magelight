<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 18.11.12
 * Time: 13:39
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Core\Blocks\Webform\Elements;

class LabeledRadio extends LabeledCheckbox
{
    /**
     * Constructor
     */
    public function __forge()
    {
        $this->_checkbox = Radio::forge();
        $this->addContent($this->_checkbox);
        $this->addClass('radio');
        $this->addContent('&nbsp;');
    }

    public function getId()
    {
        return $this->_checkbox->getId();
    }
}
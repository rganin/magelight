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
 * @method static \Magelight\Core\Blocks\Webform\Form forge() forge a webform
 * @method static \Magelight\Core\Blocks\Webform\Fieldset forgeFieldset()
 * @method static \Magelight\Core\Blocks\Webform\Row forgeRow()
 */
class Form extends Elements\Abstraction\Element
{
    protected $_tag = 'form';

    /**
     * @param $name
     * @param $action
     * @param string $type
     * @param string $method
     * @return Form
     */
    public function setConfigs($name, $action, $type = 'multipart/form-data', $method = 'post')
    {
        return $this->setAttribute('name', $name)
            ->setAttribute('action', $action)
            ->setAttribute('type', $type)
            ->setAttribute('method', $method);
    }

    public function addFieldset(Fieldset $fieldset)
    {
        return $this->addContent($fieldset);
    }

    public function addButton(Elements\Button $button)
    {
        return $this->addContent($button);
    }

    public static function __callStatic($name, $arguments)
    {
        if (substr($name, 0, 5) === 'forge') {
            $class = str_replace('forge', '', $name);
            return call_user_func(['Elements\\' . $class, 'forge']);
        }
        return false;
    }

    /**
     * @return Form
     */
    public function setHorizontal()
    {
        return $this->addClass('form-horizontal');
    }

    /**
     * @return Form
     */
    public function setInline()
    {
        return $this->addClass('form-inline');
    }
}

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
    /**
     * Element tag
     *
     * @var string
     */
    protected $_tag = 'form';

    protected $_wrapIndex = '';

    /**
     * Set form configuration
     *
     * @param string $name
     * @param string $action
     * @param string $type
     * @param string $method
     * @return Form
     */
    public function setConfigs($name, $action, $type = 'multipart/form-data', $method = 'post')
    {
        $this->_wrapIndex = $name;
        return $this->setAttribute('name', $name)
            ->setAttribute('action', $action)
            ->setAttribute('type', $type)
            ->setAttribute('method', $method);
    }

    /**
     * Wrap field name with form name
     *
     * @param string $name
     * @return string
     */
    public function wrapName($name)
    {
        return preg_replace('/^([^\[]*)/i', $this->_wrapIndex . '[\\1]', $name);
    }

    /**
     * Add content to form
     *
     * @param Elements\Abstraction\Element|string $content
     * @return Form
     */
    public function addContent($content)
    {
        if ($content instanceof \Magelight\Core\Blocks\Webform\Elements\Abstraction\Element) {
            parent::addContent($content->bindForm($this));
        }
        return $this;
    }

    /**
     * Add fieldset to form
     *
     * @param Fieldset $fieldset
     * @return Form
     */
    public function addFieldset(Fieldset $fieldset)
    {
        return $this->addContent($fieldset);
    }

    /**
     * Add button to form
     *
     * @param Elements\Button $button
     * @return Form
     */
    public function addButton(Elements\Button $button)
    {
        return $this->addContent($button);
    }

    /**
     * Call static magic (not fully implemented yet)
     *
     * @param string $name
     * @param array $arguments
     * @return bool|mixed
     */
    public static function __callStatic($name, $arguments)
    {
        if (substr($name, 0, 5) === 'forge') {
            $class = str_replace('forge', '', $name);
            return call_user_func(['Elements\\' . $class, 'forge']);
        }
        return false;
    }

    /**
     * Set form horizontal orientation according to Twitter Bootstrap class
     *
     * @return Form
     */
    public function setHorizontal()
    {
        return $this->addClass('form-horizontal');
    }

    /**
     * Set form inline class
     *
     * @return Form
     */
    public function setInline()
    {
        return $this->addClass('form-inline');
    }


}

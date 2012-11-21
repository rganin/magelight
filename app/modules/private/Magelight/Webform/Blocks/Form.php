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
 * @method static \Magelight\Webform\Blocks\Form forge() forge a webform
 * @method static \Magelight\Webform\Blocks\Fieldset forgeFieldset()
 * @method static \Magelight\Webform\Blocks\Row forgeRow()
 */
class Form extends Elements\Abstraction\Element
{
    /**
     * Element tag
     *
     * @var string
     */
    protected $_tag = 'form';

    /**
     * Wrap index
     *
     * @var string
     */
    protected $_wrapIndex = '';

    /**
     * Fields IDs that were filled from request
     *
     * @var array
     */
    protected $_filledIds = [];

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
        return self::wrapFieldName($name, $this->_wrapIndex);
    }

    /**
     * Wrap field name to array form representation
     *
     * @param string $name
     * @param string $wrapper
     * @return mixed
     */
    public static function wrapFieldName($name, $wrapper)
    {
        return preg_replace('/^([^\[]*)/i', $wrapper . '[\\1]', $name);
    }

    /**
     * Add content to form
     *
     * @param Elements\Abstraction\Element|string $content
     * @return Form
     */
    public function addContent($content)
    {
        if ($content instanceof \Magelight\Webform\Blocks\Elements\Abstraction\Element) {
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

    /**
     * Load form from request
     *
     * @param \Magelight\Http\Request $request
     * @return Form
     */
    public function loadFromRequest(\Magelight\Http\Request $request)
    {
        $method = $this->getAttribute('method', 'post');
        $methodName = 'get' . ucfirst(strtolower($method));
        if (!empty($this->_wrapIndex)) {
            $requestFields = $request->$methodName($this->_wrapIndex, []);
        } else {
            $methodName .= 'Array';
            $requestFields = $request->$methodName();
        }
        return $this->setFormValuesFromRequestFields($requestFields);
    }

    /**
     * Set form values from request object
     *
     * @param array $requestFields
     * @param string $wrapper
     * @return Form
     */
    public function setFormValuesFromRequestFields($requestFields, $wrapper = '')
    {

        foreach ($requestFields as $fieldName => $fieldValue) {
            if (is_array($fieldValue)) {
                $this->setFormValuesFromRequestFields($fieldValue, $fieldName);
            } else {
                if (!empty($wrapper)) {
                    $name = $this->wrapFieldName($fieldName, $wrapper);
                } else {
                    $name = $fieldName;
                }
                $id = $this->getFieldIdByName($name, $this->_filledIds);
                if (!empty($id)) {
                    if (isset(self::$_registeredIds[$id])
                        &&
                        self::$_registeredIds[$id] instanceof Elements\Abstraction\Field
                    ) {
                        $field = self::$_registeredIds[$id];
                        /* @var $field Elements\Abstraction\Field*/
                        $field->setValue($fieldValue);
                        $this->_filledIds = $id;
                    }
                }
            }

        }
        return $this;
    }

    /**
     * Get field ID by it`s name
     *
     * @param string $name
     * @param array $skipIds - id`s to skip while scanning
     * @return null|string
     */
    public function getFieldIdByName($name, $skipIds = [])
    {
        foreach (self::$_registeredIds as $id => $field) {
            /* @var $field Elements\Abstraction\Field*/
            if ($field->getAttribute('name') === $name) {
                if (!isset($skipIds[$id])) {
                    return $id;
                }
            }
        }
        return null;
    }
}

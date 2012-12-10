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
 * @method \Magelight\Webform\Blocks\Form addClass($class)
 * @method \Magelight\Webform\Blocks\Form setClass($class)
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
     * Feild values loaded from request
     *
     * @var array
     */
    protected $_requestFields = [];

    /**
     * Feild values loaded from request with plaintext addresses
     *
     * @var array
     */
    protected $_requestFieldsPlain = [];

    /**
     * Validator object
     *
     * @var null|\Magelight\Webform\Models\Validator
     */
    protected $_validator = null;

    /**
     * Result object
     *
     * @var null|\Magelight\Webform\Blocks\Elements\Abstraction\Element
     */
    protected $_resultRow = null;

    /**
     * Is form data loaded from request flag
     *
     * @var bool
     */
    protected $_loadedFromRequest = false;

    /**
     * Form data (loaded from request)
     *
     * @var array
     */
    protected $_formData = [];

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
     * Is form request empty
     *
     * @return bool
     */
    public function isEmptyRequest()
    {
        return empty($this->_requestFields);
    }

    /**
     * Get for request fields
     *
     * @return array
     */
    public function getRequestFields()
    {
        return $this->_requestFields;
    }

    /**
     * Add button to form
     *
     * @param array $buttons
     * @return Form
     */
    public function addButtonsRow($buttons = [])
    {
        if (!is_array($buttons)) {
            $buttons = [$buttons];
        }
        $row = Row::forge()->addField($buttons);
        return $this->addContent($row);
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
     * Set form data
     *
     * @param \Magelight\Webform\Models\Validator $validator
     * @return Form
     */
    public function setValidator(\Magelight\Webform\Models\Validator $validator)
    {
        $this->_validator = $validator;
        return $this;
    }

    /**
     * Validate form with validator set by setValidator() method
     *
     * @return bool
     * @throws \Magelight\Exception
     */
    public function validate()
    {
        if ($this->_validator instanceof \Magelight\Webform\Models\Validator) {
            if (!empty($this->_requestFields)) {
                $result = $this->_validator->validate($this->_requestFields)->result();
                if (!$result->isSuccess()) {
                    foreach ($result->getErrors() as $error) {
                        $this->addResult($error->getErrorString());
                    }
                }
                return $result->isSuccess();
            } else {
                return true;
            }
        } else {
            throw new \Magelight\Exception("Form validator is not set");
        }
    }

    /**
     * Create result row for form
     *
     * @param bool $insertToContent - inser result row to content ad the place it was created
     *
     * @return Form
     */
    public function createResultRow($insertToContent = false)
    {
        $this->_resultRow = Elements\Abstraction\Element::forge()->setTag('div');
        if ($insertToContent) {
            $this->addContent($this->_resultRow);
        }
        return $this;
    }

    /**
     * Get form result row
     *
     * @return Elements\Abstraction\Element|null
     */
    public function getResultRow()
    {
        return $this->_resultRow;
    }

    /**
     *
     *
     * @return null|string
     */
    public function getResultRowHtml()
    {
        $res = $this->getResultRow();
        if ($res instanceof \Magelight\Block) {
            return $res->toHtml();
        }
        return null;
    }

    public function setFieldValue($index, $value)
    {
        $address = $this->queryStringToArray($index);
        $this->_setFieldValueRecursive($address, $value, $this->_requestFields);
        return $this;
    }

    protected function _setFieldValueRecursive($address, $value, &$fields)
    {
        if (!is_array($address)) {
            return $this;
        }
        $index = array_shift($address);
        if (!isset($fields[$index])) {
            $fields[$index] = [];
        }
        if (empty($address)) {
            $fields[$index] = $value;
        } else {
            $pointer =  &$fields[$index];
            $this->_setFieldValueRecursive($address, $value, $pointer);
        }
        return $this;
    }

    /**
     * Get form field value
     *
     * @param string $index
     * @param mixed $default
     * @return mixed
     */
    public function getFieldValue($index, $default = null)
    {
        $address = $this->queryStringToArray($index);
        return $this->_getFieldValueRecursive($address, $default, $this->_requestFields);
    }

    /**
     * Get field value recursively
     *
     * @param string $address
     * @param mixed $default
     * @param array $fields
     * @return mixed|null
     */
    protected function _getFieldValueRecursive($address, $default = null, &$fields = [])
    {
        if (!is_array($address)) {
            return $default;
        }
        $index = array_shift($address);
        if (isset($fields[$index])) {
            $pointer =  &$fields[$index];
        } else {
            return $default;
        }
        if (empty($address)) {
            return $pointer;
        } else {
            return $this->_getFieldValueRecursive($address, $default, $pointer);
        }
    }

    /**
     * Turn query string to array
     *
     * @param string $queryString
     * @return array
     */
    public function queryStringToArray($queryString)
    {
        return explode('[', str_replace(']', '', $queryString));
    }

    /**
     * Add form result
     *
     * @param string $text
     * @param string $class
     * @return Form
     */
    public function addResult($text = '', $class = 'alert-error')
    {
        $res = Result::forge()->setContent($text)->setClass('alert')->addClass($class);
        if (!$this->_resultRow instanceof Elements\Abstraction\Element) {
            $this->createResultRow(false);
        }
        $this->_resultRow->addContent($res);
        return $this;
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
            $this->_requestFields = $request->$methodName($this->_wrapIndex, []);
        } else {
            $methodName .= 'Array';
            $this->_requestFields = $request->$methodName();
        }
        return $this->setFormValuesFromRequestFields($this->_requestFields);
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
                        $field->setFieldValueFromRequest($fieldValue);
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

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
    protected $tag = 'form';

    /**
     * Wrap index
     *
     * @var string
     */
    protected $wrapIndex = '';

    /**
     * Fields IDs that were filled from request
     *
     * @var array
     */
    protected $filledIds = [];

    /**
     * Feild values loaded from request
     *
     * @var array
     */
    protected $requestFields = [];

    /**
     * Upload request fields
     *
     * @var array
     */
    protected $requestUploads = [];

    /**
     * Feild values loaded from request with plaintext addresses
     *
     * @var array
     */
    protected $requestFieldsPlain = [];

    /**
     * Validator object
     *
     * @var null|\Magelight\Webform\Models\Validator
     */
    protected $validator = null;

    /**
     * Result object
     *
     * @var null|\Magelight\Webform\Blocks\Elements\Abstraction\Element
     */
    protected $resultRow = null;

    /**
     * Is form data loaded from request flag
     *
     * @var bool
     */
    protected $loadedFromRequest = false;

    /**
     * Form data (loaded from request)
     *
     * @var array
     */
    protected $formData = [];

    /**
     * Set form configuration
     *
     * @param string $name
     * @param string $action
     * @param string $enctype
     * @param string $method
     * @return Form
     */
    public function setConfigs($name, $action = '', $enctype = 'multipart/form-data', $method = 'post')
    {
        $this->wrapIndex = $name;
        return $this->setAttribute('name', $name)
            ->setAttribute('action', $action)
            ->setAttribute('enctype', $enctype)
            ->setAttribute('method', $method);
    }

    /**
     * Validate form on frontend flag
     *
     * @param array $ruleset - set of rules to be passed to front validator
     * @return Form
     */
    public function validateOnFront($ruleset = [])
    {
        \Magelight\Core\Blocks\Document::getInstance()
            ->addJs('Magelight/Webform/static/js/jquery-validation.js');
        \Magelight\Core\Blocks\Document::getInstance()
            ->addJs('Magelight/Webform/static/js/ajax-form.js');
        $this->setAttribute('data-front-validate', 'true');
        $this->setAttribute(
            'data-validator-rules',
            $this->validator->getValidationRulesJson($this->getAttribute('name')), $this::QUOTATION_SINGLE
        );
        $this->setAttribute(
            'data-validator-messages',
            $this->validator->getValidationMessagesJson($this->getAttribute('name')), $this::QUOTATION_SINGLE
        );
        return $this;
    }

    /**
     * Wrap field name with form name
     *
     * @param string $name
     * @return string
     */
    public function wrapName($name)
    {
        return self::wrapFieldName($name, $this->wrapIndex);
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
        } else {
            parent::addContent($content);
        }
        return $this;
    }

    /**
     * Get element by name
     *
     * @param string $name
     *
     * @return Elements\Abstraction\Element|null
     */
    public function getElementByName($name)
    {
        return $this->getElementById($this->getFieldIdByName($name));
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
        return empty($this->requestFields);
    }

    /**
     * Get for request fields
     *
     * @return array
     */
    public function getRequestFields()
    {
        return $this->requestFields;
    }

    /**
     * Add button to form
     *
     * @param array|Elements\Abstraction\Element $buttons
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
        return $this;
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
        $this->validator = $validator;
        $this->validator->setForm($this);
        return $this;
    }

    /**
     * Get validator instance
     *
     * @return \Magelight\Webform\Models\Validator|null
     */
    public function getValidator()
    {
        if (!$this->validator instanceof \Magelight\Webform\Models\Validator) {
            $validator = \Magelight\Webform\Models\Validator::forge();
            $this->setValidator($validator);
        }
        return $this->validator;
    }

    /**
     * Validate form with validator set by setValidator() method
     *
     * @return bool
     * @throws \Magelight\Exception
     */
    public function validate()
    {
        if ($this->validator instanceof \Magelight\Webform\Models\Validator) {
            if (!empty($this->requestFields)) {
                $fieldsResult = $this->processValidation(array_merge($this->requestFields, (array)$this->requestUploads));
                return $fieldsResult;
            } else {
                return true;
            }
        } else {
            throw new \Magelight\Exception("Form validator is not set");
        }
    }

    /**
     * Process data validation
     *
     * @param array $data
     * @return bool
     */
    protected function processValidation($data)
    {
        $result = $this->validator->validate($data)->result();
        if (!$result->isSuccess()) {
            foreach ($result->getErrors() as $error) {
                /** @var $error \Magelight\Webform\Models\Validation\Error */
                $this->addResult($error->getErrorString());
            }
        }
        return $result->isSuccess();
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
        $this->resultRow = Elements\Abstraction\Element::forge()->setTag('div')->addClass('form-result');
        if ($insertToContent) {
            $this->addContent($this->resultRow);
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
        return $this->resultRow;
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

    /**
     * Set field value
     *
     * @param string $index
     * @param mixed $value
     * @return Form
     */
    public function setFieldValue($index, $value)
    {
        $address = $this->queryStringToArray($index);
        $this->_setFieldValueRecursive($address, $value, $this->requestFields);
        $this->setFormValuesFromRequestFields($this->requestFields);
        return $this;
    }

    /**
     * Set field values recursive
     *
     * @param string $address
     * @param mixed $value
     * @param array $fields
     * @return Form
     */
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
        return $this->getFieldValueRecursive($address, $default, $this->requestFields);
    }

    /**
     * Get field value recursively
     *
     * @param string $address
     * @param mixed $default
     * @param array $fields
     * @return mixed|null
     */
    protected function getFieldValueRecursive($address, $default = null, &$fields = [])
    {
        if (!is_array($address)) {
            return $default;
        }
        $index = array_shift($address);
        if (isset($fields[$index])) {
            @$pointer =  &$fields[$index];
        } else {
            return $default;
        }
        if (empty($address)) {
            return $pointer;
        } else {
            return $this->getFieldValueRecursive($address, $default, $pointer);
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
    public function addResult($text = '', $class = 'alert-danger')
    {
        $res = Result::forge()->setContent($text)->setClass('alert')->addClass($class);
        if (!$this->resultRow instanceof Elements\Abstraction\Element) {
            $this->createResultRow(false);
        }
        $this->resultRow->addContent($res);
        return $this;
    }

    /**
     * Load form from request
     *
     * @param \Magelight\Http\Request $request
     * @return Form
     */
    public function loadFromRequest(\Magelight\Http\Request $request = null)
    {
        if (empty($request)) {
            $request = \Magelight\Http\Request::getInstance();
        }
        $method = $this->getAttribute('method', 'post');
        $methodName = 'get' . ucfirst(strtolower($method));
        if (!empty($this->wrapIndex)) {
            $this->requestFields = $request->$methodName($this->wrapIndex, []);
            $this->requestUploads = $request->getFilesNormalized($this->wrapIndex, []);
        } else {
            $methodName .= 'Array';
            $this->requestFields = $request->$methodName();
            $this->requestUploads = $request->getFilesArrayNormalized();
        }
        return $this->setFormValuesFromRequestFields($this->requestFields);
    }

    /**
     * Set form values from Data array
     *
     * @param array $data
     */
    public function setFormValues($data = [])
    {
        $this->requestFields = $data;
        $this->setFormValuesFromRequestFields($this->requestFields);
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
                $id = $this->getFieldIdByName($name, $this->filledIds);
                if (!empty($id)) {
                    if (isset(self::$registeredIds[$id])
                        &&
                        self::$registeredIds[$id] instanceof Elements\Abstraction\Field
                    ) {
                        $field = self::$registeredIds[$id];
                        /* @var $field Elements\Abstraction\Field*/
                        $field->setFieldValueFromRequest($fieldValue);
                        $this->filledIds = $id;
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
        foreach (self::$registeredIds as $id => $field) {
            /* @var $field Elements\Abstraction\Field*/
            if ($field->getAttribute('name') === $name) {
                if (!isset($skipIds[$id])) {
                    return $id;
                }
            }
        }
        return $this->generateIdFromName($name);
    }

    /**
     * Fetch upload data
     *
     * @param string $index
     * @param array $default
     * @return array
     */
    public function getUpload($index, $default = [])
    {
        $address = $this->queryStringToArray($index);
        return $this->getFieldValueRecursive($address, $default, $this->requestUploads);
    }

    /**
     * Fetch upload object from form
     *
     * @param string $index
     * @return \Magelight\Upload|null
     */
    public function getUploadObject($index)
    {
        $address = $this->queryStringToArray($index);
        $array = $this->getFieldValueRecursive($address, [], $this->requestUploads);
        if (isset($array['name'], $array['tmp_name'], $array['error'], $array['size'], $array['type'])) {
            return \Magelight\Upload::forge($array);
        }
        return null;
    }

    /**
     * Fetch upload object from form
     *
     * @param string $index
     * @return \Magelight\Upload[]|[]
     */
    public function getUploadObjectsArray($index)
    {
        $address = $this->queryStringToArray($index);
        $array = $this->getFieldValueRecursive($address, [], $this->requestUploads);
        $result = [];
        if (!is_array($array)) {
            return $result;
        }
        foreach ($array as $key => $uploadData) {
            if (isset(
                $uploadData['name'],
                $uploadData['tmp_name'],
                $uploadData['error'],
                $uploadData['size'],
                $uploadData['type']
            )) {
                $result[$key] = \Magelight\Upload::forge($uploadData);
            }
        }
        return $result;
    }

    /**
     * Save form values to user`s session
     *
     * @return Form
     */
    public function saveToSession()
    {
        \Magelight\Http\Session::getInstance()->set('forms-' . $this->getAttribute('name'), $this->getRequestFields());
        return $this;
    }

    /**
     * Load user data from session
     *
     * @return Form
     */
    public function loadFromSession()
    {
        $data = \Magelight\Http\Session::getInstance()->get('forms-' . $this->getAttribute('name'), []);
        if (!empty($data)) {
            $this->requestFields = $data;
        }
        return $this;
    }
}

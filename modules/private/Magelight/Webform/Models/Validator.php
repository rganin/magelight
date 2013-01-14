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

namespace Magelight\Webform\Models;

/**
 * @method static \Magelight\Webform\Models\Validator forge() - forg teh validator
 */
class Validator extends \Magelight\Model
{
    /**
     * Empty field data constant
     */
    const EMPTY_DATA = null;

    /**
     * Validation result
     *
     * @var Validation\Result
     */
    protected $_result = null;

    /**
     * Checkers for fields
     *
     * @var array
     */
    protected $_checkers = [];

    /**
     * Checkers for fields group
     *
     * @var array
     */
    protected $_groupCheckers = [];

    /**
     * Field checkers with flat (query string) indexes
     *
     * @var array
     */
    protected $_flatNameCheckers = [];

    /**
     * Break on first error flag
     *
     * @var bool
     */
    protected $_breakOnFirst = false;

    /**
     * Errors limit to render
     *
     * @var int
     */
    protected $_errorsLimit = 10000;

    /**
     * Data to validate
     *
     * @var array
     */
    protected $_data = [];

    /**
     * Validate data
     *
     * @param array $data
     * @return Validator
     */
    public function validate($data)
    {
        $this->_result = Validation\Result::forge(true, []);
        $this->_data = &$data;
        return $this->_validateRecursive($this->_data, $this->_checkers);
    }

    /**
     * Validate data recursively
     *
     * @param array $data
     * @param array $checkers
     * @return Validator
     */
    protected function _validateRecursive(&$data, &$checkers = [])
    {
        foreach ($checkers as $fieldName => $checker) {

            $validationData = empty($fieldName) ? $data :
                (isset($data[$fieldName]) ? $data[$fieldName] : self::EMPTY_DATA);


            if (is_array($checker)) {
                $validationData = empty($fieldName) ? $data :
                    (isset($data[$fieldName]) ? $data[$fieldName] : [self::EMPTY_DATA]);

                foreach ($checker as $key => $subChecker) {
                    if (empty($key)) {
                        foreach ($validationData as $dataField) {
                            $this->_validateRecursive($dataField, $checker);
                        }
                    } else {
                        $subChecker = [$key=> $subChecker];
                        $this->_validateRecursive($validationData, $subChecker);
                    }
                }
            } elseif ($checker instanceof Validation\Checker) {
                $this->_processValidation($validationData, $checker);
            }
        }
        return $this;
    }

    /**
     * Process data validation
     *
     * @param string $data
     * @param Validation\Checker $checker
     * @return Validator
     */
    protected function _processValidation($data, Validation\Checker $checker)
    {
        if ($this->isEmptyField($data) && !$checker->hasRuleRequired() && !$checker->hasPernanentValidation()) {

        } else {
            if ($this->_breakOnFirst) {
                $checker->breakOnFirst(true);
            }
            if (!$checker->check($data)) {
                if ($this->_errorsLimit) {
                    foreach ($checker->getErrors() as $error) {
                        if ($this->_errorsLimit--) {
                            $this->_result->addError($error);
                        } else {
                            break;
                        }
                    }
                }
                $this->_result->setFail();
                if ($this->_breakOnFirst) {
                    return $this;
                }
            }
        }
        return $this;
    }

    /**
     * Check is string empty
     *
     * @param string $value
     * @return bool
     */
    public function isEmptyField($value)
    {
        if (is_array($value)) {
            return empty($value);
        }
        $value = trim($value);
        return $value === '' || $value === null;
    }

    /**
     * Set break on first error flag
     *
     * @param bool $flag
     * @return Validator
     */
    public function breakOnFirst($flag = true)
    {
        $this->_breakOnFirst = $flag;
        return $this;
    }

    public function setErrorsLimit($limit = 10000)
    {
        $this->_errorsLimit = $limit;
        return $this;
    }

    /**
     * get result
     *
     * @return Validation\Result
     */
    public function result()
    {
        return $this->_result;
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
     * Set checker by field address array
     *
     * @param array $fieldAddress
     * @param Validation\Checker $checker
     * @return Validator
     */
    public function setChecker(array $fieldAddress, Validation\Checker $checker)
    {
        if (empty($fieldAddress)) {
            return $this;
        }

        $index = array_shift($fieldAddress);
        if (!isset($this->_checkers[$index])) {
            $this->_checkers[$index] = null;
        }

        $pointer = &$this->_checkers[$index];

        foreach ($fieldAddress as $index) {
            if (!isset($pointer[$index])) {
                $pointer[$index] = [];
            }
            $pointer = &$pointer[$index];
        }

        $pointer = $checker;
        return $this;
    }

    /**
     * Set checker with flat address
     *
     * @param string $flatFieldAddress
     * @param Validation\Checker $checker
     * @return Validator
     */
    public function setFlatChecker($flatFieldAddress, Validation\Checker $checker)
    {
        $this->_flatNameCheckers[$flatFieldAddress] = $checker;
        return $this;
    }

    /**
     * Get checker by address
     *
     * @param array $fieldAddress
     * @return Validation\Checker|null
     */
    protected function _getChecker(array $fieldAddress)
    {
        if (empty($fieldAddress)) {
            return null;
        }
        $index = array_shift($fieldAddress);
        if (!isset($this->_checkers[$index])) {
            return null;
        }

        $pointer = &$this->_checkers[$index];
        foreach ($fieldAddress as $index) {
            if (!isset($pointer[$index])) {
                return null;
            } else {
                $pointer = &$pointer[$index];
            }
        }
        return $pointer instanceof Validation\Checker ? $pointer : null;
    }

    /**
     * Add field rules
     *
     * @param string $fieldName
     * @param null $fieldAlias
     * @return Validation\Checker
     */
    public function fieldRules($fieldName, $fieldAlias = null)
    {
        $fieldAddress = $this->queryStringToArray($fieldName);
        $fieldAddressLast = array_reverse($fieldAddress)[0];
        $checker = $this->_getChecker($fieldAddress);
        if (empty($checker)) {
            $checker = Validation\Checker::forge($fieldAddressLast, $fieldAlias, $this)
                ->breakOnFirst($this->_breakOnFirst);
            $this->setChecker($fieldAddress, $checker);
            $this->setFlatChecker($fieldName, $checker);
        }
        return $checker;
    }

    /**
     * Get default ruleset for front validation
     *
     * @return array
     */
    public function getDefaultFrontValidationRuleset()
    {
        return [
            'min',
            'max',
            'minlength',
            'maxlength',
            'required',
            'range',
            'rangelength',
            'number',
            'email',
            'url',
            'remote',
            'equalTo'
        ];
    }

    public function getValidationRulesJson($formName, $ruleset = [])
    {
        if (empty($ruleset)) {
            $ruleset = $this->getDefaultFrontValidationRuleset();
        }
        $rules = new \stdClass();
        foreach ($this->_flatNameCheckers as $fieldName => $checker) {
            /* @var $checker \Magelight\Webform\Models\Validation\Checker */
            $fieldName = \Magelight\Webform\Blocks\Form::wrapFieldName($fieldName, $formName);
            $rules->$fieldName = $checker->getRulesJson($ruleset);
        }
        return json_encode($rules, JSON_FORCE_OBJECT | JSON_UNESCAPED_SLASHES);
    }

    public function getValidationMessagesJson($formName, $ruleset = [])
    {
        if (empty($ruleset)) {
            $ruleset = $this->getDefaultFrontValidationRuleset();
        }
        $messages = new \stdClass();
        foreach ($this->_flatNameCheckers as $fieldName => $checker) {
            /* @var $checker \Magelight\Webform\Models\Validation\Checker */
            $fieldName = \Magelight\Webform\Blocks\Form::wrapFieldName($fieldName, $formName);
            $messages->$fieldName = $checker->getRulesMessagesJson($ruleset);
        }
        return json_encode($messages, JSON_FORCE_OBJECT | JSON_UNESCAPED_SLASHES);
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
        return $this->getFieldValueRecursive($address, $default, $this->_requestFields);
    }

    /**
     * Get field value recursively
     *
     * @param string $address
     * @param mixed $default
     * @param array $fields
     * @return mixed|null
     */
    public function getFieldValueRecursive($address, $default = null, &$fields = [])
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

    public function groupRules(array $fieldsGroup = [])
    {
        $index = implode('+', $fieldsGroup);
        
    }
}

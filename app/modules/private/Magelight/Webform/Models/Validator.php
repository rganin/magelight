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
     * Break on first error flag
     *
     * @var bool
     */
    protected $_breakOnFirst = false;

    /**
     * Validate data
     *
     * @param array $data
     * @return Validator
     */
    public function validate($data)
    {
        $this->_result = Validation\Result::forge(true, []);
        return $this->_validateRecursive($data, $this->_checkers);
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


            if (is_array($checker) && is_array($validationData)) {

                foreach ($checker as $key => $subChecker) {
                    if (empty($key)) {
                        foreach ($validationData as $dataField) {
                            $this->_validateRecursive($dataField, $checker);
                        }
                    } else {
                        $this->_validateRecursive($validationData, $checker);
                    }
                }

            } elseif ($checker instanceof Validation\Checker) {
                /* @var $checker Validation\Checker*/
                if (!$checker->check($validationData)) {
                    $this->_result->addErrors($checker->getErrors())->setFail();
                    if ($this->_breakOnFirst) {
                        return $this;
                    }
                }
            }
        }
        return $this;
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
            $pointer[$index] = [];
            $pointer = &$pointer[$index];
        }

        $pointer = $checker;
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
        $checker = $this->_getChecker($fieldAddress);
        if (empty($checker)) {
            $checker = Validation\Checker::forge($fieldName, $fieldAlias);
            $this->setChecker($fieldAddress, $checker);
        }
        return $checker;
    }
}

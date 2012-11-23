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
     * @var null
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
     * @param $data
     * @return Validator
     */
    public function validate($data)
    {
        $result = true;
        $this->_result = Validation\Result::forge($result, []);
        foreach ($this->_checkers as $fieldName => $checker) {
            /* @var $checker Validation\Checker*/
            if (!isset($data[$fieldName])) {
                $data[$fieldName] = self::EMPTY_DATA;
            }
            if (!$checker->check($data[$fieldName])) {
                $this->_result->addErrors($checker->getErrors())->setFail();
                if ($this->_breakOnFirst) {
                    return $this;
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
     * Add field rules
     *
     * @param $fieldName
     * @param null $fieldAlias
     * @return Validation\Checker
     */
    public function fieldRules($fieldName, $fieldAlias = null)
    {
        if (isset($this->_checkers[$fieldName]) && $this->_checkers[$fieldName] instanceof Validation\Checker) {
            return $this->_checkers[$fieldName];
        }
        $checker = Validation\Checker::forge($fieldName, $fieldAlias);
        $this->_checkers[$fieldName] = $checker;
        return $checker;
    }
}

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
 * @method static \Magelight\Webform\Models\Validator forge()
 */
class Validator{

    use \Magelight\Forgery;

	/**
	 * Array of checkers for fields
	 *
	 * @var array
	 */
    private $checkers = [];

    /**
     * Envelope for all fields
     *
     * @var string
     */
    private $envelope = null;


    /**
     * Validation errors
     *
     * @var array
     */
    protected $_errors = [];

    /**
     * Break on first error
     *
     * @var bool
     */
    protected $_breakOnFirst = false;


    /**
     * Add the field rule
     *
     * @param string $field - field name
     *
     * @return Validation\Checker
     */
    public function fieldRule($field)
    {
        $this->checkers[$field]= Validation\Checker::forge($field, $this);
        return $this->checkers[$field];
    }

    /**
     * Break validation on first error
     *
     * @param bool $break
     * @return Validator
     */
    public function breakOnFirst($break = true)
    {
        $this->_breakOnFirst = $break;
        return $this;
    }

    /**
     * Check is validator break on first error flag enabled
     *
     * @return bool
     */
    public function isBreakOnFirst()
    {
        return $this->_breakOnFirst;
    }

    /**
     * Validate data
     *
     * @param array $data
     * @return boolean
     */
    public function check($data)
    {
    	
        $res = true;
        foreach ($this->checkers as $field => $checker) {
            /* @var $checker \Magelight\Webform\Models\Validation\Checker*/
            if (!empty($this->envelope)) {
                $checker->envelope($this->envelope);
            }
            $res &= $checker->check(isset($data[$field]) ? $data[$field] : null);
        }
        return $res;
    }

    /**
     * Add an envelope for all fields, overrides all fields envelopes
     *
     * @param string $envelopePattern
     * @return Validator
     */
    public function envelope($envelopePattern = null)
    {
        foreach ($this->checkers as $checker) {
            /* @var $checker \Magelight\Webform\Models\Validation\Checker*/
           $checker->envelope($envelopePattern);
        }
    }

    /**
     * Add error to stack
     *
     * @param string $errorStr
     */
    public function addError($errorStr)
    {
        $this->_errors[] = $errorStr;
    }

    /**
     * Get validation errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }
}

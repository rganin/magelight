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

namespace Magelight\Core\Models;

/**
 * @method static \Magelight\Core\Models\Validator forge()
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
     * Validate data
     *
     * @param array $data
     * @return boolean
     */
    public function check($data)
    {
    	
        $res = true;
        foreach ($this->checkers as $field => $checker) {
            /* @var $checker \Magelight\Core\Models\Validation\Checker*/
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
            /* @var $checker \Magelight\Core\Models\Validation\Checker*/
           $checker->envelope($envelopePattern);
        }
    }

    public function addError($errorStr)
    {
        $this->_errors[] = $errorStr;
    }

    public function getErrors()
    {
        return $this->_errors;
    }
}
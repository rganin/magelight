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

namespace Magelight\Core\Models\Validation;

/**
 * Form field checker
 *
 * @author iddqd
 * @method static \Magelight\Core\Models\Validation\Checker forge($fieldName, $validatorObject)
 * @method \Magelight\Core\Models\Validation\Checker date() - check is a field a valid date
 * @method \Magelight\Core\Models\Validation\Checker dateRange($dateStart, $dateEnd)
 * - check is the field between 2 dates (inclusive)
 * @method \Magelight\Core\Models\Validation\Checker email() - the field must be a valid email
 * @method \Magelight\Core\Models\Validation\Checker float() - the field must be a valid float number
 * @method \Magelight\Core\Models\Validation\Checker lengthRange($floor, $ceil)
 * - the field must contain symbols between mentioned as params
 * @method \Magelight\Core\Models\Validation\Checker min($minValue) - chek minimal value
 * @method \Magelight\Core\Models\Validation\Checker minLength($minLength) - the field must be longer or equal
 * @method \Magelight\Core\Models\Validation\Checker max($maxValue) - chek minimal value
 * @method \Magelight\Core\Models\Validation\Checker dateAndTime() - field must be a valid date
 * @method \Magelight\Core\Models\Validation\Checker numeric() - the field must be numeric
 * @method \Magelight\Core\Models\Validation\Checker pregMatch($regex) - the field must match regular expression
 * @method \Magelight\Core\Models\Validation\Checker range($minValue, $maxValue) - chek minimal and maximum value
 * @method \Magelight\Core\Models\Validation\Checker required()  required() - field is required
 * @method \Magelight\Core\Models\Validation\Checker url() - the field must be a valid url address
 */
class Checker
{
    use \Magelight\Forgery;

	/**
	 * Field key
	 *
	 * @var string
	 */
    protected $fieldName;

    /**
     * Title for the field
     * if not set the field key will be passed as the title
     *
     * @var string
     */
    protected $fieldTitle;

    /**
     * Rules stack
     *
     * @var array
     */
    protected $rules = [];

    /**
     * Validator object
     *
     * @var \Magelight\Core\Models\Validator
     */
    protected $_validator = null;

    /**
     * Flag that field is an array of fields
     *
     * @var bool
     */
    protected $isArray = false;

    /**
     * Envelope pattern
     * e.g. form[%0]
     *
     * @var string
     */
    protected $envelopePattern = null;

    /**
     * Forgery constructor
     *
     * @param $fieldName
     * @param \Magelight\Core\Models\Validator $validator
     */
    public function __forge($fieldName, \Magelight\Core\Models\Validator $validator)
    {
        $this->fieldName = $fieldName;
        $this->_validator = $validator;
    }

    /**
     * Tell validator that the field is an array of common fields
     *
     * @return Checker
     */
    public function isArray()
    {
        $this->isArray = true;
        return $this;
    }

    /**
     * Set field title. If not set the field array key will be passed as title
     *
     * @param string $fieldTitle
     * @return Checker
     */
    public function title($fieldTitle = null)
    {
        $this->fieldTitle = $fieldTitle;
        return $this;
    }

    /**
     * Add an envelope to field for highlighting
     * Is useful when field is an array part
     * example:
     * 		if you user u[name] , u[login], u[email] and u[password]
     * 		for posting data and getting it as $_POST['u']
     * 		you can add an envelope like u[%0] and receive
     *      a highlight name as it is set in your html code
     *
     *
     * @param string $envelopePattern
     * @return Checker
     */
    public function envelope($envelopePattern = null)
    {
        if (empty($this->envelopePattern)) {
            $this->envelopePattern = $envelopePattern;
        }
        return $this;
    }

    /**
     * Call magic
     *
     * @param string $name
     * @param array $arguments
     * @return Checker
     */
    public function __call($name, $arguments)
    {
        $name = '\\Magelight\\Core\\Models\\Validation\\Rules\\' . ucfirst($name);
            $this->rules[$name]['checker'] = call_user_func([$name, 'forge']);
            $this->rules[$name]['arguments'] = $arguments;
        return $this;
    }

    /**
     * Check value to match validation rules
     *
     * @param mixed $value
     * @return boolean
     */
    public function check($value)
    {
        $res = true;

        foreach ($this->rules as $rule) {
            $result = true;
            if (!$this->isArray || ($this->isArray && !is_array($value))) {
                $result = call_user_func_array([$rule['checker'], 'check'], [$value, $rule['arguments']]);
            } else {
                foreach ($value as $field) {
                    $result = call_user_func_array([$rule['checker'], 'check'], [$field, $rule['arguments']]);
                }
            }

            if (!$result) {
                $this->raiseError($rule);
            }
            $res &= $result;
        }

        return $res;
    }

    /**
     * Raise an error and push it into errors stack
     *
     * @param string $rule
     */
    public function raiseError($rule)
    {
        $fieldTitle = empty($this->fieldTitle) ? $this->fieldName : $this->fieldTitle;
        if (isset($rule['error'])) {
            $error = $rule['error'];
        } else {
            $error = call_user_func_array(array($rule['checker'], 'getError'), array());
        }
        $args = $rule['arguments'];
        array_unshift($args, $fieldTitle);
        var_dump($args);
        $error = \Magelight::__($error, 1, 'validation', array_values($args));
        $this->_validator->addError($error);
    }

    /**
     * Set error text for rule
     *
     * @param string $ruleName
     * @param string $errorString
     * @return Checker
     */
    public function setError($ruleName, $errorString)
    {
        $name = '\\Magelight\\Core\\Models\\Validation\\Rules\\' . ucfirst($ruleName);
        $this->rules[$name]['error'] = $errorString;
        return $this;
    }


    /**
     * Set errors array('rule' => 'Error text')
     * Example:
     * array(
     * 		'range' => 'Oooops, value is out of range',
     * 		'url' => 'Url validation for field %0 failed'
     * );
     *
     * @param array $errors
     * @return Checker
     */
    public function setErrorsArray($errors = array())
    {
    	foreach ($errors as $key => $error) {
    		$this->rules[$key]['error'] = $error;
    	}
    	return $this;
    }
}

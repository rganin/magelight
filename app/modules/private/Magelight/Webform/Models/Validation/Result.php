<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 23.11.12
 * Time: 22:36
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Webform\Models\Validation;

/**
 * @method static  \Magelight\Webform\Models\Validation\Result forge($success, $errors = [])
 */
class Result
{
    use \Magelight\Forgery;

    /**
     * Success flag
     *
     * @var bool
     */
    protected $_success = true;

    /**
     * Array of error objects
     *
     * @var array
     */
    protected $_errors = [];

    /**
     * Forgery constructor
     *
     * @param bool $success
     * @param array $errors
     */
    public function __forge($success, $errors = [])
    {
        $this->_success = (bool) $success;
        $this->addErrors($errors);
    }

    /**
     * Add errors to result
     *
     * @param array $errors
     * @return Result
     */
    public function addErrors($errors = [])
    {
        foreach ($errors as $error) {
            if ($error instanceof Error) {
                $this->_errors[] = $error;
            }
        }
        return $this;
    }

    /**
     * Set result as failed
     *
     * @return Result
     */
    public function setFail()
    {
        $this->_success = false;
        return $this;
    }

    /**
     * Set result as success
     *
     * @return Result
     */
    public function setSuccess()
    {
        $this->_success = true;
        return $this;
    }

    /**
     * Is result successful
     *
     * @return bool
     */
    public function isSuccess()
    {
        return $this->_success;
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

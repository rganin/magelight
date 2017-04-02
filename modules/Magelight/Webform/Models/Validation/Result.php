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
 * @copyright Copyright (c) 2013 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight\Webform\Models\Validation;

/**
 * @method static $this forge($success, $errors = [])
 */
class Result
{
    use \Magelight\Traits\TForgery;

    /**
     * Success flag
     *
     * @var bool
     */
    protected $success = true;

    /**
     * Array of error objects
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Forgery constructor
     *
     * @param bool $success
     * @param Error $error
     */
    public function __forge($success, $error = null)
    {
        $this->success = (bool) $success;
        $this->addError($error);
    }

    /**
     * Add errors to result
     *
     * @param Error $error
     * @return Result
     */
    public function addError($error)
    {
        if ($error instanceof Error) {
            $this->errors[] = $error;
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
        $this->success = false;
        return $this;
    }

    /**
     * Set result as success
     *
     * @return Result
     */
    public function setSuccess()
    {
        $this->success = true;
        return $this;
    }

    /**
     * Is result successful
     *
     * @return bool
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * Get validation errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}

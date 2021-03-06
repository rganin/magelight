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
 * @method static $this forge($errorString, $highlightId) - forge error
 */
class Error
{
    use \Magelight\Traits\TForgery;

    /**
     * Error string
     *
     * @var string
     */
    protected $errorString = '';

    /**
     * Highlight element ID
     *
     * @var string
     */
    protected $highlightId = '';

    /**
     * Forgery constructor
     *
     * @param string $errorString
     * @param string $highlightId
     */
    public function __forge($errorString, $highlightId)
    {
        $this->errorString = $errorString;
        $this->highlightId = $highlightId;
    }

    /**
     * Get error string
     *
     * @return string
     */
    public function getErrorString()
    {
        return $this->errorString;
    }
}

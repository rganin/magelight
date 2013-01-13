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
 * @method static \Magelight\Webform\Models\Validation\Error forge($errorString, $highlightId) - forge error
 */
class Error
{
    use \Magelight\TForgery;

    /**
     * Error string
     *
     * @var string
     */
    protected $_errorString = '';

    /**
     * Highlight element ID
     *
     * @var string
     */
    protected $_highlightId = '';

    /**
     * Forgery constructor
     *
     * @param string $errorString
     * @param string $highlightId
     */
    public function __forge($errorString, $highlightId)
    {
        $this->_errorString = $errorString;
        $this->_highlightId = $highlightId;
    }

    /**
     * Get error string
     *
     * @return string
     */
    public function getErrorString()
    {
        return $this->_errorString;
    }
}
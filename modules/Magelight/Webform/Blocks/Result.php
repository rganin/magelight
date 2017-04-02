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
 * @copyright Copyright (c) 2012-2015 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight\Webform\Blocks;

/**
 * @method static $this forge()
 */
class Result extends Elements\Abstraction\Element
{
    /**
     * {@inheritdoc}
     */
    protected $tag = 'div';

    /**
     * Forgery constructor
     */
    public function __forge()
    {
        $this->addClass('alert');
    }

    /**
     * Set class to 'error'
     *
     * @return Elements\Abstraction\Element
     */
    public function setErrorClass()
    {
        return $this->addClass('alert-danger');
    }

    /**
     * Set class to warning
     *
     * @return Elements\Abstraction\Element
     */
    public function setWarningClass()
    {
        return $this->addClass('alert-warning');
    }

    /**
     * set class to info
     *
     * @return Elements\Abstraction\Element
     */
    public function setInfoClass()
    {
        return $this->addClass('alert-info');
    }

    /**
     * Set class to success
     *
     * @return Elements\Abstraction\Element
     */
    public function setSuccessClass()
    {
        return $this->addClass('alert-success');
    }
}

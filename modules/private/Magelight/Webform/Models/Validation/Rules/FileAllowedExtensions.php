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

namespace Magelight\Webform\Models\Validation\Rules;

class FileAllowedExtensions extends AbstractRule
{
    /**
     * Check value with rule
     * Returns:
     *    - true if rule passed.
     *    - false if value doesn`t match the rule.
     *
     * @param mixed $value
     * @return bool
     */
    public function check($value)
    {
        if (empty($value) || empty($value['name'])) {
            return true;
        }
        foreach ($this->_arguments[0] as $extension) {
            if (preg_match('/^.*' . $extension . '$/i', $value['name'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get error
     *
     * @return string
     */
    public function getError()
    {
        if (!empty($this->_error)) {
            return $this->_error;
        }
        return __('File %s must have valid extension', $this->getErrorArguments());
    }
}

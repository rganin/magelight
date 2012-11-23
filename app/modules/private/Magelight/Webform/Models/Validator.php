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
     * Validate data
     *
     * @param $data
     * @return Validator
     */
    public function validate($data)
    {
        return $this;
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
        $checker = Validation\Checker::forge($fieldName, $fieldAlias);
        $this->_checkers = $checker;
        return $checker;
    }
}

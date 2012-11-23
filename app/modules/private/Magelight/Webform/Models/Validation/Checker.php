<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category
 * @package
 * @subpackage
 * @author
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magelight\Webform\Models\Validation;

/**
 * @method static \Magelight\Webform\Models\Validation\Checker forge($fieldName, $fieldAlias = null) - forgery
 * @method \Magelight\Webform\Models\Validation\Rules\AbstractRule max($maxValue) - maximum value rule
 * @method \Magelight\Webform\Models\Validation\Rules\AbstractRule required() - Field is required
 */
class Checker
{
    use \Magelight\Forgery;

    /**
     * Field name
     *
     * @var string
     */
    protected $_fieldName = null;

    /**
     * Field alias
     *
     * @var string
     */
    protected $_fieldAlias = null;

    /**
     * Rules to check with
     *
     * @var array
     */
    protected $_rules = [];

    /**
     * Forgery constructor
     *
     * @param $fieldName
     * @param string $fieldAlias
     */
    public function __forge($fieldName, $fieldAlias = null)
    {
        $this->_fieldName = $fieldName;
        if (empty($fieldAlias)) {
            $this->_fieldAlias = $fieldName;
        }
    }

    /**
     * Add rule to checker
     *
     * @param Rules\AbstractRule $rule
     * @return Checker
     */
    public function addRule(Rules\AbstractRule $rule)
    {
        $this->_rules[] = $rule;
        return $this;
    }

    /**
     * Call magix
     *
     * @param $name
     * @param $arguments
     * @return Rules\AbstractRule
     * @throws \Magelight\Exception
     */
    public function __call($name, $arguments)
    {
        $className = __NAMESPACE__ . '\\Rules\\' . ucfirst($name);
        $rule = call_user_func_array([$className, 'forge'], [$this]);

        if (!$rule instanceof Rules\AbstractRule) {
            throw new \Magelight\Exception(
                "Trying to add unknown rule '$className' in "
                . __CLASS__
                . " for field {$this->_fieldName} ({$this->_fieldAlias})."
            );
        }
        /* @var $rule Rules\AbstractRule */
        $this->addRule($rule->setArguments($arguments)->setFieldTitle($this->_fieldAlias));
        return $rule;
    }

    public function check($value)
    {
        $result = true;
        foreach ($this->_rules as $rule) {
            /* @var $rule Rules\AbstractRule */
            $result = $result & $rule->check($value);
        }

        return (bool) $result;
    }
}

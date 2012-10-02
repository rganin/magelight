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

namespace Magelight;

class Error
{
    /**
     * Error title
     *
     * @var string
     */
    protected $_title = '';

    /**
     * Constructor
     *
     * @param $title
     * @param int $level
     */
    public function __construct($title, $level = E_USER_ERROR)
    {
        switch ($level) {
            case E_USER_NOTICE || E_NOTICE:
                $this->_title .= "NOTICE: ";
                break;
            case E_USER_WARNING || E_COMPILE_WARNING || E_WARNING:
                $this->_title .= "WARNING: ";
                break;
            case E_USER_ERROR || E_STRICT || E_DEPRECATED || E_CORE_ERROR || E_ERROR:
                $this->_title .= "ERROR: ";
                break;
        }
        $this->_title .= $title;
    }

    /**
     * fetch error
     *
     * @return string
     */
    public function __toString()
    {
        return "<div style=\"border: 1px #f00 solid; background-color: #fadddd; color: black;\">
        {$this->_title}
        </div>
        ";
    }
}

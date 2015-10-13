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

namespace Magelight\Auth\Blocks;

/**
 * @method static \Magelight\Auth\Blocks\UloginWidget forge()
 */
class UloginWidget extends \Magelight\Block
{
    /**
     * Widget template
     *
     * @var string
     */
    protected $_template = 'Magelight/Auth/templates/ulogin-widget.phtml';

    /**
     * Configuration node name
     *
     * @var string
     */
    protected $_index = \Magelight\App::DEFAULT_INDEX;

    /**
     * Set block configuration index
     *
     * @param string $configIndex
     * @return UloginWidget
     */
    public function setConfigIndex($configIndex)
    {
        $this->_index = $configIndex;
        return $this;
    }

    /**
     * Get widget data from config
     *
     * @return string
     */
    public function getUloginData()
    {
        $config = (array) \Magelight\Config::getInstance()->getConfig(
            'global/auth/ulogin/instances/' . $this->_index . '/options'
        );


        if (isset($config['redirect_route'])) {
            $config['redirect_uri'] = $this->url($config['redirect_route']);
            unset($config['redirect_route']);
        }
        foreach ($config as $key => $configItem) {
            $config[$key] = $key . '=' . (string) $configItem;
        }
        return implode(';', $config);
    }

    /**
     * Get widget HTML ID from config
     *
     * @return string
     */
    public function getUloginHtmlId()
    {
        return (string) \Magelight\Config::getInstance()->getConfig(
            'global/auth/ulogin/instances/' . $this->_index . '/html_id'
        );
    }

    /**
     * Get Ulogin JS url
     *
     * @return string
     */
    public function getUloginScriptUrl()
    {
        return (string) \Magelight\Config::getInstance()->getConfig('global/auth/ulogin/ulogin_script_url');
    }
}

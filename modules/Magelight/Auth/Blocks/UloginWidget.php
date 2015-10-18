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
    protected $template = 'Magelight/Auth/templates/ulogin-widget.phtml';

    /**
     * Configuration node name
     *
     * @var string
     */
    protected $index = \Magelight\App::DEFAULT_INDEX;

    /**
     * Set block configuration index
     *
     * @param string $configIndex
     * @return UloginWidget
     */
    public function setConfigIndex($configIndex)
    {
        $this->index = $configIndex;
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
            'global/auth/ulogin/instances/' . $this->index . '/options'
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
            'global/auth/ulogin/instances/' . $this->index . '/html_id'
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

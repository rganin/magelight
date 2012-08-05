<?php
/**
 * $$name_placeholder_notice$$
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
 * @version $$version_placeholder_notice$$
 * @uthor $$author_placeholder_notice$$
 * @copyright Copyright (c) 2012 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Bike\Helpers;

/**
 * Url helper class
 * @static getInstance \Bike\Helpers\UrlHelper 
 */
class UrlHelper extends \Bike\Prototypes\Singleton
{
    /**
     * Url types
     */
    const TYPE_HTTP = 'http';
    const TYPE_HTTPS = 'https';
    
    protected $_app = null;

    /**
     * Constructor
     */
    protected function __construct()
    {
        $this->_app = \Bike::app();
    }

    /**
     * Get bike base URL
     * 
     * @return string
     */
    public function getBaseUrl($type = self::TYPE_HTTP, $trim = false)
    {
        $domain = $this->_app->getConfig('global/base_domain', null);
        if (is_null($domain)) {
            $server = \Bike\Http\Server::getInstance();
            /* @var \Bike\Http\Server $server*/
            $domain = $server->getCurrentDomain();
        }
        return $type . '://' . $domain;
    }
    
//    public function 
}
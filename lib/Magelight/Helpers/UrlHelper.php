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

namespace Magelight\Helpers;

/**
 * Url helper class
 * @static getInstance \Magelight\Helpers\UrlHelper
 */
class UrlHelper
{
    use \Magelight\TForgery;

    /**
     * Url types
     */
    const TYPE_HTTP  = 'http';
    const TYPE_HTTPS = 'https';
    
    /**
     * Application
     * 
     * @var \Magelight\App
     */
    protected $_app = null;

    /**
     * Get bike base URL
     * 
     * @param string $type
     * 
     * @return string
     */
    public function getBaseUrl($type = self::TYPE_HTTP)
    {
        $domain = \Magelight::app()->config()->getConfig('global/base_domain', null);
        if (is_null($domain)) {
            $server = \Magelight\Http\Server::getInstance();
            $domain = $server->getCurrentDomain();
        } elseif (is_array($domain)) {
            $domain = array_shift($domain);
        }
        return $type . '://' . (string) $domain;
    }

    /**
     * Get url by match
     * 
     * @param string $match
     * @param array  $params
     * @param string $type
     *
     * @return string
     * @throws \Magelight\Exception
     */
    public function getUrl($match, $params = [], $type = self::TYPE_HTTP)
    {
        $match = '/' . trim($match, '\\/');

        if (\Magelight::app()->isInDeveloperMode() && !$this->checkParamsWithPlaceholderMask($match, $params)) {
            throw new \Magelight\Exception("Passed url params don`t match route mask.", E_USER_NOTICE);
        }
                
        return $this->getBaseUrl($type) . $this->makeRequestUri($match, $params);
    }

    /**
     * Check Url params by mask
     * 
     * @param string $match
     * @param array $params
     *
     * @return bool
     */
    protected function checkParamsWithPlaceholderMask($match, $params) 
    {
        if (preg_match_all(\Magelight\Components\Loaders\Routes::MATCH_REGEX, $match, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $name = $match['name'];
                $mask = isset($match['regex']) ? $match['regex'] : \Magelight\Components\Loaders\Routes::DEFAULT_REGEX;
                if (isset($params[$name])) {
                    if (!preg_match("/^([{$mask}]*)$/", $params[$name])) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * Set url params to match placeholders
     * 
     * @param string $match
     * @param array $params
     *
     * @return mixed
     */
    protected function setParamsToPlaceholders($match, &$params = [])
    {
        foreach ($params as $key => $value) {
            $match = preg_replace("/(\{{$key}\}|\{{$key}:[^\}]*\})/", $value, $match);
            unset($params[$key]);
        }
        $match = preg_replace("/(\{[^\}]*\})/", '', $match); //cleaning not used placeholders
        return $match;
    }

    /**
     * Create request URI from match and params
     *
     * @param string $match
     * @param string $params
     * @return string
     */
    protected function makeRequestUri($match, $params)
    {
        $paramsTmp = $params;
        $match = $this->setParamsToPlaceholders($match, $paramsTmp);

        return !empty($paramsTmp) ? ($match . '?' . http_build_query($paramsTmp)) : $match;
    }

    /**
     * Get URL for static data
     * 
     * @param string $module
     * @param string $type
     * @param string $path
     *
     * @return string
     */
    public function getStaticUrl($module, $type, $path = '')
    {
        return $module . '/' . $type . '/' . $path;
    }
}

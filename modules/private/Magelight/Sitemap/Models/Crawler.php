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
 * @copyright Copyright (c) 2013 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight\Sitemap\Models;

/**
 * Class Crawler
 * @package Magelight\Sitemap\Models
 *
 * @method static \Magelight\Sitemap\Models\Crawler forge($startUrl, Sitemap $sitemapObject)
 */
class Crawler
{
    /**
     * Urls to process
     *
     * @var array
     */
    protected $_urls = [];

    /**
     * Processed urls in sitemap format
     *
     * @var array
     */
    protected $_processedUrls = [];

    /**
     * @var Sitemap
     */
    protected $_sitemap;

    /**
     * Using forgery
     */
    use \Magelight\Traits\TForgery;

    /**
     * Forgery constructor
     *
     * @param string $startUrl
     * @param Sitemap $sitemapObject
     */
    public function __forge($startUrl, Sitemap $sitemapObject)
    {
        $this->_urls[] = $startUrl;
        $this->_sitemap = $sitemapObject;
    }

    /**
     * Crawl
     */
    public function crawl()
    {
        while (!empty($this->_urls)) {
            $url = array_shift($this->_urls);
            if (!$this->isUrlProcessed($url)) {
                if ($content = $this->_loadPage($url)) {
                    $links = @$this->_fetchLinksFromContent($content);
                    $this->appendUrls($links);
                    $this->_setUrlProcessed($url);
                }
            }
        }
    }

    /**
     * Add urls to process
     *
     * @param array $urls
     * @return $this
     */
    protected function appendUrls(array $urls)
    {
        foreach ($urls as $url) {
            if ($this->_canProcessUrl($url)) {
                $this->_urls[] = $url;
            }
        }
        return $this;
    }

    /**
     * Set url as processed
     *
     * @param string $url
     * @return $this
     */
    protected function _setUrlProcessed($url)
    {
        $this->_processedUrls[$url] = 1;
        return $this;
    }

    /**
     * Is url already processed
     *
     * @param string $url
     * @return bool
     */
    public function isUrlProcessed($url)
    {
        return isset($this->_processedUrls[$url]);
    }


    /**
     * Get processed urls
     *
     * @return array
     */
    public function getProcessedUrls()
    {
        return $this->_processedUrls;
    }

    /**
     * Get processed urls alias
     *
     * @return array
     */
    public function getFoundUrls()
    {
        return $this->getProcessedUrls();
    }

    /**
     * Fetch links from HTML content
     *
     * @param string $content
     * @return array
     */
    protected function _fetchLinksFromContent($content)
    {
        $links = [];
        $dom = new \DOMDocument();
        $dom->loadHTML($content);
        foreach($dom->getElementsByTagName('a') as $link) {
            /** @var $link \DOMElement */
            if ($link->getAttribute('rel') !== 'nofollow') {
                $links[] = $link->getAttribute('href');
            }
        }
        unset($dom);
        return array_unique($links);
    }

    /**
     * Can process desired url
     *
     * @param string $url
     * @return bool
     */
    protected function _canProcessUrl($url)
    {
        return $this->_sitemap->isUrlAllowed($url) && !$this->_sitemap->isUrlDisallowed($url);
    }

    /**
     * Load page content and cut out <body> tag
     *
     * @param string $url
     * @return null|string
     */
    protected function _loadPage($url)
    {
        $content = @file_get_contents($url);
        if (preg_match('/<body>(.*)<\/body>/s', $content, $matches)) {
            if (isset($matches[1])) {
                $content = $matches[1];
            } else {
                $content = null;
            }
        }
        return $content;
    }
}

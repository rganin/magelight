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
    protected $urls = [];

    /**
     * Processed urls in sitemap format
     *
     * @var array
     */
    protected $processedUrls = [];

    /**
     * @var Sitemap
     */
    protected $sitemap;

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
        $this->urls[] = $startUrl;
        $this->sitemap = $sitemapObject;
    }

    /**
     * Crawl site by urls
     */
    public function crawl()
    {
        while (!empty($this->urls)) {
            $url = array_shift($this->urls);
            if (!$this->isUrlProcessed($url)) {
                if ($content = $this->loadPage($url)) {
                    $links = @$this->fetchLinksFromContent($content);
                    $this->appendUrls($links);
                    $this->setUrlProcessed($url);
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
            if ($this->canProcessUrl($url)) {
                $this->urls[] = $url;
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
    protected function setUrlProcessed($url)
    {
        $this->processedUrls[$url] = 1;
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
        return isset($this->processedUrls[$url]);
    }


    /**
     * Get processed urls
     *
     * @return array
     */
    public function getProcessedUrls()
    {
        return $this->processedUrls;
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
    protected function fetchLinksFromContent($content)
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
    protected function canProcessUrl($url)
    {
        return $this->sitemap->isUrlAllowed($url) && !$this->sitemap->isUrlDisallowed($url);
    }

    /**
     * Load page content and cut out <body> tag
     *
     * @param string $url
     * @return null|string
     */
    protected function loadPage($url)
    {
        $content = file_get_contents($url);
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

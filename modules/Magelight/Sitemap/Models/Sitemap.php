<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 11.12.13
 * Time: 23:21
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Sitemap\Models;

/**
 * Class Sitemap
 * @package Magelight\Sitemap\Models
 *
 * <?xml version="1.0" encoding="UTF-8"?>
 * <urlset
 *      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
 *      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 *      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
 *      http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
 * <url>
 *      <loc>http://perevezi.com.ua/</loc>
 *      <changefreq>daily</changefreq>
 *      <priority>1.00</priority>
 * </url>
 * .......
 *
 * @method static \Magelight\Sitemap\Models\Sitemap forge($startUrl)
 */
class Sitemap
{
    /**
     * URLs change frequency
     */
    const CHANGEFREQ_ALWAYS     = 'always';
    const CHANGEFREQ_HOURLY     = 'hourly';
    const CHANGEFREQ_DAILY      = 'daily';
    const CHANGEFREQ_WEEKLY     = 'weekly';
    const CHANGEFREQ_MONTHLY    = 'monthly';
    const CHANGEFREQ_YEARLY     = 'yearly';
    const CHANGEFREQ_NEVER      = 'never';

    /**
     * Using forgery
     */
    use \Magelight\Traits\TForgery;

    /**
     * Allowed urls masks
     *
     * @var array
     */
    protected $_allowedUrls = ['*'];

    /**
     * Disallowed urls masks
     *
     * @var array
     */
    protected $_disallowedUrls = [];

    /**
     * Priority masks (processed in direct way (fom firt to last))
     *
     * @var array
     */
    protected $_priorityMasks = [
        '*' => 1
    ];

    /**
     * Change frequency masks (processed in direct way (fom firt to last))
     *
     * @var array
     */
    protected $_changeFrequencyMasks = [
        '*' => 'daily'
    ];

    /**
     * Array of urls in sitemap format
     */
    protected $_urls = [];

    /**
     * @var Crawler
     */
    protected $_crawler;

    /**
     * Forgery constructor
     *
     * @param string $startUrl
     */
    public function __forge($startUrl)
    {
        $this->_crawler = Crawler::forge($startUrl, $this);
    }

    /**
     * Set urls priority masks
     *
     * @param array $priorityMasks
     * @return $this
     */
    public function setPriorityMasks(array $priorityMasks = ['*' => 1])
    {
        $this->_priorityMasks = $priorityMasks;
        return $this;
    }

    /**
     * Set urls change frequency masks
     *
     * @param array $changeFrequencyMasks
     * @return $this
     */
    public function setChangeFrequencyMasks(array $changeFrequencyMasks = ['*' => 'daily'])
    {
        $this->_changeFrequencyMasks = $changeFrequencyMasks;
        return $this;
    }

    /**
     * Set allowed urls
     *
     * @param array $wildcardMaskArray - array of wildcards
     * @return $this
     */
    public function allowUrls(array $wildcardMaskArray = ['*'])
    {
        $this->_allowedUrls = $wildcardMaskArray;
        return $this;
    }

    /**
     * Set disallowed urls
     *
     * @param array $wildcardMaskArray - array of wildcards
     * @return $this
     */
    public function disallowUrls(array $wildcardMaskArray = [])
    {
        $this->_disallowedUrls = $wildcardMaskArray;
        return $this;
    }

    /**
     * Get url priority
     *
     * @param string $url
     * @return float
     */
    protected function _getUrlPriority($url)
    {
        foreach ($this->_priorityMasks as $priorityMask => $priority) {
            if (fnmatch($priorityMask, $url)) {
                return $priority;
            }
        }
        return 0.5;
    }

    /**
     * Get URL change frequency
     *
     * @param string $url
     * @return string
     */
    protected function _getUrlChangeFrequency($url)
    {
        foreach ($this->_changeFrequencyMasks as $changeFrequencyMask => $frequency) {
            if (fnmatch($changeFrequencyMask, $url)) {
                return $frequency;
            }
        }
        return 'daily';
    }

    /**
     * Is url allowed
     *
     * @param string $url
     * @return bool
     */
    public function isUrlAllowed($url)
    {
        $result = true;
        foreach ($this->_allowedUrls as $mask) {
            $result &= fnmatch($mask, $url);
        }
        return $result;
    }

    /**
     * Is url disallowed
     *
     * @param string $url
     * @return bool
     */
    public function isUrlDisallowed($url)
    {
        foreach ($this->_disallowedUrls as $mask) {
            if (fnmatch($mask, $url)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Generate sitemap
     *
     * @return $this
     */
    public function generate()
    {
        $this->_crawler->crawl();
        foreach ($this->_crawler->getFoundUrls() as $url => $found) {
            $this->_urls[] = [
                'loc' => $url,
                'priority' => $this->_getUrlPriority($url),
                'changefreq' => $this->_getUrlChangeFrequency($url),
            ];
        }
        return $this;
    }

    /**
     * Get sitemap urls
     *
     * @return array
     */
    public function getSitemapArray()
    {
        return $this->_urls;
    }

    /**
     * Save sitemap as XML
     *
     * @param string $filename
     */
    public function saveAsXml($filename)
    {
        $header = '<?xml version="1.0" encoding="UTF-8"?>
<urlset
      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
        $mapUrls = [];
        foreach ($this->getSitemapArray() as $url) {
            $mapUrls[] = "<url>
  <loc>{$url['loc']}</loc>
  <changefreq>{$url['changefreq']}</changefreq>
  <priority>{$url['priority']}</priority>
</url>";
        }
        $mapUrls = implode(PHP_EOL, $mapUrls);
        file_put_contents($filename, $header . PHP_EOL . $mapUrls . PHP_EOL . '</urlset>');
    }
}
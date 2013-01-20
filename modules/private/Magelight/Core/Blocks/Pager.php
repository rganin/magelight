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

namespace Magelight\Core\Blocks;

/**
 * @method static \Magelight\Core\Blocks\Pager forge(\Magelight\Db\Collection $collection = null)
 */
class Pager extends \Magelight\Block
{
    /**
     * Page variable URL template
     */
    const PAGE_TEMPLATE  = '{page:0-9}';

    /**
     * Page URI route
     *
     * @var string
     */
    protected $_route = '?page={page:0-9}';

    /**
     * Additional route params
     *
     * @var array
     */
    protected $_routeParams = [];

    /**
     * Items per page
     *
     * @var int
     */
    protected $_perPage = 10;

    /**
     * Total items count
     *
     * @var int
     */
    protected $_total = 0;

    /**
     * Current page
     *
     * @var int
     */
    protected $_currentPage = 0;

    /**
     * Pager current page siblings count
     *
     * @var int
     */
    protected $_siblings = 2;

    /**
     * Collection
     *
     * @var \Magelight\Db\Collection
     */
    protected $_collection = null;

    /**
     * Element attributes
     *
     * @var array
     */
    protected $_attributes = [];

    /**
     * Pager templates
     *
     * @var string
     */
    protected $_template = 'Magelight/Core/templates/pager.phtml';

    /**
     * Forgery constructor
     *
     * @param \Magelight\Db\Collection $collection - collection to build pager for
     */
    public function __forge(\Magelight\Db\Collection $collection)
    {
        $this->_collection = $collection;
        $this->addClass('pagination');
        if ($this->_collection instanceof \Magelight\Db\Collection) {
            $this->setTotal($this->_collection->totalCount());
            $this->setPerPage($this->_collection->getLimit());
            $this->setCurrentPage(floor($this->_collection->getOffset() / $this->_perPage));
        }
        $this->setNextCaption()->setPrevCaption();
    }

    /**
     * Set element`s attribute
     *
     * @param string $attribute
     * @param mixed $value
     * @return AjaxPager
     */
    public function setAttribute($attribute, $value = null)
    {
        $this->_attributes[$attribute] = $value;
        return $this;
    }

    /**
     * Render form attributes as HTML attributes code
     *
     * @return string
     */
    public function renderAttributes()
    {
        $render = '';
        foreach ($this->_attributes as $name => $attr) {
            $render .= ' ' . $name . '=' . '"' . $attr . '"';
        }
        return $render;
    }

    /**
     * Set max per page count
     *
     * @param int $perPage
     * @return Pager
     */
    public function setPerPage($perPage = 10)
    {
        $this->_perPage = $perPage;
        return $this;
    }

    /**
     * Set total count
     *
     * @param int $total
     * @return Pager
     */
    public function setTotal($total = 0)
    {
        $this->_total = $total;
        return $this;
    }

    /**
     * Set pager current page
     *
     * @param int $currentPage
     * @return Pager
     */
    public function setCurrentPage($currentPage = 0)
    {
        $this->_currentPage = $currentPage;
        return $this;
    }

    /**
     * Set caption for previous page navigation button
     *
     * @param string $caption
     * @return Pager
     */
    public function setPrevCaption($caption = '&#x2190;')
    {
        $this->set('prev_caption', $caption);
        return $this;
    }

    /**
     * Set caption for next page nav button
     *
     * @param string $caption
     * @return Pager
     */
    public function setNextCaption($caption = '&#x2192;')
    {
        $this->set('next_caption', $caption);
        return $this;
    }

    /**
     * Set pager class
     *
     * @param string $class
     * @return Pager
     */
    public function setClass($class = 'pagination')
    {
        $this->setAttribute('class', $class);
        return $this;
    }

    /**
     * Get element`s attribute
     *
     * @param string $name
     * @param string $default
     * @return string
     */
    public function getAttribute($name, $default = '')
    {
        return isset($this->_attributes[$name]) ? $this->_attributes[$name] : $default;
    }

    /**
     * Add pager class
     *
     * @param string $class
     * @return Pager
     */
    public function addClass($class)
    {
        $this->setAttribute('class', $this->getAttribute('class', '') . ' ' . $class);
        return $this;
    }

    /**
     * Remove pager class
     *
     * @param string $class
     * @return Pager
     */
    public function removeClass($class)
    {
        $this->setAttribute('class', str_ireplace($class, '', $this->getAttribute('class', '')));
        return $this;
    }

    /**
     * Get default route template
     *
     * @return string
     */
    public function getDefaultRouteTemplate()
    {
        return '?page=' . self::PAGE_TEMPLATE;
    }

    /**
     * Set pager route
     *
     * @param string|null $route
     * @param array $routeParams
     *
     * @return Pager
     */
    public function setRoute($route = null, $routeParams = [])
    {
        if (empty($route)) {
            $route = $this->getDefaultRouteTemplate();
        }
        $this->_route = $route;
        $this->_routeParams = $routeParams;
        return $this;
    }

    /**
     * Get page URL
     *
     * @param int $page
     * @return string
     */
    public function getPageUrl($page = 0)
    {
        if (strstr($this->_route, self::PAGE_TEMPLATE)) {
            return $this->url(str_ireplace(self::PAGE_TEMPLATE, $page, $this->_route), $this->_routeParams);
        }
        $this->_routeParams['page'] = $page;
        return $this->url($this->_route, $this->_routeParams);
    }

    /**
     * Initialize block
     *
     * @return \Magelight\Block|void
     */
    public function init()
    {
        $pages = [];
        $pagesCount = ceil($this->_total / $this->_perPage);
        if ($this->prev_caption) {
            $pages[] = [
                'page'    => $this->_currentPage,
                'caption' => $this->prev_caption,
                'url'     => $this->getPageUrl($this->_currentPage - 1),
                'active'  => false,
                'disabled' => $this->_currentPage <= 0,
            ];
        }
        $start = ($this->_currentPage - $this->_siblings) > 0
            ? ($this->_currentPage - $this->_siblings)
            : 0;

        $finish = ($this->_currentPage + $this->_siblings +1) < $pagesCount
            ? ($this->_currentPage + $this->_siblings +1)
            : $pagesCount;

        for ($i = $start; $i < $finish; $i++) {
            $pages[] = [
                'page'    => $i,
                'caption' => $i + 1,
                'url'     => $this->getPageUrl($i),
                'active' => $i == (int) $this->_currentPage,
                'disabled' => false,
            ];
        }
        if ($this->next_caption) {
            $pages[] = [
                'page'    => $this->_currentPage,
                'caption' => $this->next_caption,
                'url'     => $this->getPageUrl($this->_currentPage + 1),
                'active' => false,
                'disabled' => $this->_currentPage >= $pagesCount -1,
            ];
        }
        $this->set('pages', $pages);
        unset($pages);
        return parent::init();
    }
}

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

namespace Magelight\Core\Blocks;

/**
 * @method static \Magelight\Core\Blocks\Pager forge(\Magelight\Db\Collection $collection = null)
 */
class Pager extends \Magelight\Block
{
    /**
     * Page variable URL template
     */
    const PAGE_TEMPLATE  = '{%1$s:0-9}';

    /**
     * Page argument name
     */
    const PAGE_ARGUMENT_NAME = 'page';

    /**
     * Default route template
     */
    const ROUTE_TEMPLATE = '?%1$s={%1$s:0-9}';

    /**
     * Page URI route
     *
     * @var string
     */
    protected $route;

    /**
     * Additional route params
     *
     * @var array
     */
    protected $routeParams = [];

    /**
     * Items per page
     *
     * @var int
     */
    protected $perPage = 10;

    /**
     * Total items count
     *
     * @var int
     */
    protected $total = 0;

    /**
     * Current page
     *
     * @var int
     */
    protected $currentPage = 0;

    /**
     * Pager current page siblings count
     *
     * @var int
     */
    protected $siblingsCount = 2;

    /**
     * Collection
     *
     * @var \Magelight\Db\Collection
     */
    protected $collection;

    /**
     * Element attributes
     *
     * @var string[]
     */
    protected $attributes = [];

    /**
     * Show first and last pages
     *
     * @var bool
     */
    protected $showFirstLast = true;

    /**
     * Pager templates
     *
     * @var string
     */
    protected $template = 'Magelight/Core/templates/pager.phtml';

    /**
     * Forgery constructor
     *
     * @param \Magelight\Db\Collection $collection - collection to build pager for
     */
    public function __forge(\Magelight\Db\Collection $collection = null)
    {
        $this->collection = $collection;
        $this->addClass('pagination');
        if ($this->collection instanceof \Magelight\Db\Collection) {
            $this->setTotal($this->collection->totalCount());
            $this->setPerPage($this->collection->getLimit());
            $this->setCurrentPage($this->perPage ? floor($this->collection->getOffset() / $this->perPage) : 1);
        }
        $this->setNextCaption()->setPrevCaption()->setFirstCaption()->setLastCaption();
    }

    /**
     * Set element`s attribute
     *
     * @param string $attribute
     * @param mixed $value
     * @return $this
     */
    public function setAttribute($attribute, $value = null)
    {
        $this->attributes[$attribute] = $value;
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
        foreach ($this->attributes as $name => $attr) {
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
        $this->perPage = $perPage;
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
        $this->total = $total;
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
        $this->currentPage = $currentPage;
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
     * Set caption for first page rewind
     *
     * @param string $caption
     * @return Pager
     */
    public function setFirstCaption($caption = '<<')
    {
        $this->set('first_caption', $caption);
        return $this;
    }

    /**
     * Set caption for next page nav button
     *
     * @param string $caption
     * @return Pager
     */
    public function setLastCaption($caption = '>>')
    {
        $this->set('last_caption', $caption);
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
        return isset($this->attributes[$name]) ? $this->attributes[$name] : $default;
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
        return sprintf(self::ROUTE_TEMPLATE, $this->getPageArgumentName());
    }

    /**
     * Get default route template
     *
     * @return string
     */
    public function getPageArgumentName()
    {
        return self::PAGE_ARGUMENT_NAME;
    }

    /**
     * Get default route template
     *
     * @return string
     */
    public function getPageTemplate()
    {
        return sprintf(self::PAGE_TEMPLATE, $this->getPageArgumentName());
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
        $this->route = isset($route) ? $route : $this->getDefaultRouteTemplate();
        $this->routeParams = $routeParams;
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
        if (strstr($this->route, $this->getPageTemplate())) {
            $params = $this->routeParams;
            unset($params[$this->getPageArgumentName()]);
            return $this->url(str_ireplace($this->getPageTemplate(), $page, $this->route), $params);
        }
        $this->routeParams['page'] = $page;
        return $this->url($this->route, $this->routeParams);
    }

    /**
     * Initialize block
     *
     * @return \Magelight\Block|void
     */
    protected function initBlock()
    {
        $pages = [];
        $pagesCount = ceil($this->total / $this->perPage);
        if ($this->showFirstLast) {
            $pages[] = [
                'page'    => 0,
                'caption' => $this->first_caption,
                'url'     => $this->getPageUrl(0),
                'active'  => false,
                'disabled' => $this->currentPage <= 0,
            ];
        }
        if ($this->prev_caption) {
            $pages[] = [
                'page'    => $this->currentPage,
                'caption' => $this->prev_caption,
                'url'     => $this->getPageUrl($this->currentPage - 1),
                'active'  => false,
                'disabled' => $this->currentPage <= 0,
            ];
        }
        $start = ($this->currentPage - $this->siblingsCount) > 0
            ? ($this->currentPage - $this->siblingsCount)
            : 0;

        $finish = ($this->currentPage + $this->siblingsCount +1) < $pagesCount
            ? ($this->currentPage + $this->siblingsCount +1)
            : $pagesCount;

        for ($i = $start; $i < $finish; $i++) {
            $pages[] = [
                'page'    => $i,
                'caption' => $i + 1,
                'url'     => $this->getPageUrl($i),
                'active' => $i == (int) $this->currentPage,
                'disabled' => false,
            ];
        }
        if ($this->next_caption) {
            $pages[] = [
                'page'    => $this->currentPage,
                'caption' => $this->next_caption,
                'url'     => $this->getPageUrl($this->currentPage + 1),
                'active' => false,
                'disabled' => $this->currentPage >= $pagesCount -1,
            ];
        }
        if ($this->showFirstLast) {
            $pages[] = [
                'page'    => $pagesCount - 1,
                'caption' => $this->last_caption,
                'url'     => $this->getPageUrl($pagesCount - 1),
                'active'  => false,
                'disabled' => $this->currentPage >= $pagesCount - 1,
            ];
        }
        $this->set('pages', $pages);
        unset($pages);
        return parent::initBlock();
    }

    /**
     * Set showing first and last
     *
     * @param bool $flag
     *
     * @return $this
     */
    public function setShowFirstLast($flag = true)
    {
        $this->showFirstLast = $flag;
        return $this;
    }
}

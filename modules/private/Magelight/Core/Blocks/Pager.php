<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 11.12.12
 * Time: 13:05
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Core\Blocks;

/**
 * @method static \Magelight\Core\Blocks\Pager forge(\Magelight\Db\Collection $collection = null)
 */
class Pager extends \Magelight\Block
{
    const PAGE_TEMPLATE  = '{page:0-9}';

    protected $_route = '?page={page:0-9}';

    protected $_perPage = 10;

    protected $_total = 0;

    protected $_currentPage = 0;

    protected $_siblings = 2;

    /**
     * Collection
     *
     * @var \Magelight\Db\Collection
     */
    protected $_collection = null;

    /**
     * Pager templates
     *
     * @var string
     */
    protected $_template = 'Magelight/Core/templates/pager.phtml';

    public function __forge(\Magelight\Db\Collection $collection = null)
    {
        $this->_collection = $collection;
        if ($this->_collection instanceof \Magelight\Db\Collection) {
            $this->setTotal($this->_collection->totalCount());
            $this->setPerPage($this->_collection->getLimit());
            $this->setCurrentPage(floor($this->_collection->getOffset() / $this->_perPage));
        }
        $this->setNextCaption()->setPrevCaption();
    }

    public function setPerPage($perPage = 10)
    {
        $this->_perPage = $perPage;
        return $this;
    }

    public function setTotal($total = 0)
    {
        $this->_total = $total;
        return $this;
    }

    public function setCurrentPage($currentPage = 0)
    {
        $this->_currentPage = $currentPage;
        return $this;
    }

    public function setPrevCaption($caption = '&#x2190;')
    {
        $this->set('prev_caption', $caption);
        return $this;
    }

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
        $this->set('class', $class);
        return $this;
    }

    /**
     * Add pager class
     *
     * @param string $class
     * @return Pager
     */
    public function addClass($class)
    {
        $this->set('class', $this->get('class', '') . ' ' . $class);
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
        $this->set('class', str_ireplace($class, '', $this->get('class', '')));
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
     * @return Pager
     */
    public function setRoute($route = null)
    {
        if (empty($route)) {
            $route = $this->getDefaultRouteTemplate();
        }
        $this->_route = $route;
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
        $url = $this->url(str_ireplace(self::PAGE_TEMPLATE, $page, $this->_route));
        return $url;
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

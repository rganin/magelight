<?php

namespace Magelight\Core\Blocks;

use Magelight\Block;
use Magelight\Db\Collection;

/**
 * Class Grid
 * @package Magelight\Core\Blocks
 *
 * @method static $this forge()
 */
class Grid extends Element
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var Pager
     */
    protected $pager;

    /**
     * Forgery constructor
     */
    public function __forge()
    {
        $this->setTag('div');
        $this->setClass('grid');
    }

    /**
     * @param Collection $collection
     * @return $this
     */
    public function setDataSource(Collection $collection)
    {
        $this->collection = $collection;
        $this->pager->setCollection($collection);
        return $this;
    }

    /**
     * @param int $page
     * @return $this
     */
    public function setCurrentPage($page)
    {
        $this->pager->setCurrentPage($page);
        return $this;
    }

    public function addColumn($title)
    {

    }
}

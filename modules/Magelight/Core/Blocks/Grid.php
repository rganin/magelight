<?php

namespace Magelight\Core\Blocks;

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

    public function __forge()
    {
        $this->setTag('div');
        $this->setClass('grid');
    }

    public function setDataSource(Collection $collection)
    {
        $this->collection = $collection;
        return $this;
    }

    public function setPage($page)
    {
        $this->collection->setPage($page);
        return $this;
    }
}

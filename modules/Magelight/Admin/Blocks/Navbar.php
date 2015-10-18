<?php

namespace Magelight\Admin\Blocks;

class Navbar extends \Magelight\Block
{
    /**
     * @var string
     */
    protected $template = 'Magelight/Admin/templates/navbar.phtml';

    /**
     * @var \Magelight\Admin\Blocks\Navbar\Item[]
     */
    protected $items = [];

    /**
     * Forgery constructor
     */
    public function __forge()
    {
        $this->loadItems();
    }

    /**
     * Add item
     *
     * @param int $order
     * @param \Magelight\Admin\Blocks\Navbar\Item $item
     */
    protected function addItem($order, $item)
    {
        $this->items[$order] = $item;
    }

    /**
     * Get navbar items
     *
     * @return \Magelight\Admin\Blocks\Navbar\Item[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Load navbar items
     */
    protected function loadItems()
    {
        $items = \Magelight\Config::getInstance()->getConfig('admin/navbar/items');
        $this->items = $this->getItemForgery()->forgeItems($items);
    }

    /**
     * Get item forgery
     *
     * @return \Magelight\Admin\Blocks\Navbar\Item\Forgery
     */
    protected function getItemForgery()
    {
        return \Magelight\Admin\Blocks\Navbar\Item\Forgery::getInstance();
    }
}
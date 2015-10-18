<?php

namespace Magelight\Admin\Blocks;

class Sections extends \Magelight\Block
{
    /**
     * @var string
     */
    protected $template = 'Magelight/Admin/templates/sections.phtml';

    /**
     * @var \Magelight\Admin\Blocks\Sections\Item[]
     */
    protected $items = [];

    /**
     * Forgery constructor
     *
     * @param string $itemsPath
     */
    public function __forge($itemsPath)
    {
        $this->loadItems();
    }

    /**
     * Add item
     *
     * @param int $order
     * @param \Magelight\Admin\Blocks\Sections\Item $item
     */
    public function addItem($order, $item)
    {
        $this->items[$order] = $item;
    }

    /**
     * Get items
     *
     * @return Sections\Item[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Load items
     */
    protected function loadItems()
    {
        $items = \Magelight\Config::getInstance()->getConfig('admin/navbar/items');
        $this->items = $this->getItemForgery()->forgeItems($items);
    }

    /**
     * Get item forgery
     *
     * @return \Magelight\Admin\Blocks\Sections\Item\Forgery
     */
    protected function getItemForgery()
    {
        return \Magelight\Admin\Blocks\Sections\Item\Forgery::getInstance();
    }
}
<?php

namespace Magelight\Admin\Blocks;

class Sections extends \Magelight\Block
{
    protected $_template = 'Magelight/Admin/templates/sections.phtml';

    protected $_items = [];

    public function __forge($itemsPath)
    {
        $this->_loadItems();
    }

    public function addItem($order, $item)
    {
        $this->_items[$order] = $item;
    }

    public function getItems()
    {
        return $this->_items;
    }

    protected function _loadItems()
    {
        $items = \Magelight\Config::getInstance()->getConfig('admin/navbar/items');
        $this->_items = $this->getItemForgery()->forgeItems($items);
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
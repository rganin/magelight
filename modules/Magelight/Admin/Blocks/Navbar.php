<?php

namespace Magelight\Admin\Blocks;

class Navbar extends \Magelight\Block
{
    protected $_template = 'Magelight/Admin/templates/navbar.phtml';

    protected $_items = [];

    public function __forge()
    {
        $this->_loadItems();
    }

    protected function _addItem($order, $item)
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
     * @return \Magelight\Admin\Blocks\Navbar\Item\Forgery
     */
    protected function getItemForgery()
    {
        return \Magelight\Admin\Blocks\Navbar\Item\Forgery::getInstance();
    }
}
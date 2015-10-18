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

namespace Magelight\Admin\Blocks;

/**
 * Class Navbar
 * @package Magelight\Admin\Blocks
 */
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

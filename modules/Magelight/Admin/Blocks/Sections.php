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
 * Class Sections
 * @package Magelight\Admin\Blocks
 */
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

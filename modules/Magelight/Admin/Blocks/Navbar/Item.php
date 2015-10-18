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

namespace Magelight\Admin\Blocks\Navbar;

/**
 * Class Item
 * @package Magelight\Admin\Blocks\Navbar
 */
class Item extends \Magelight\Block
{
    /**
     * @var string
     */
    protected $template = 'Magelight/Admin/templates/navbar/item.phtml';

    /**
     * Forgery constructor
     *
     * @param \SimpleXMLElement $itemConfig
     * @param Item|null $parentItem
     */
    public function __forge(\SimpleXMLElement $itemConfig, \Magelight\Admin\Blocks\Navbar\Item $parentItem = null)
    {
        $this->title = (string)$itemConfig->title;
        $this->type = (string)$itemConfig->type;
        $this->route = (string)$itemConfig->route;
        $this->class = (string)$itemConfig->class;
        $this->link_class = (string)$itemConfig->link_class;
        $this->is_bar_item = empty($parentItem);
        if (!empty($itemConfig->subitems)) {
            $this->has_subitems = true;
            $this->subitems = $this->getItemForgery()->forgeItems($itemConfig->subitems, $this);
        }
    }

    /**
     * Check item has subitems
     *
     * @return mixed
     */
    public function hasSubitems()
    {
        return $this->get('has_subitems', false);
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

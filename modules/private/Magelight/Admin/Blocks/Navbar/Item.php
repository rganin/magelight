<?php
namespace Magelight\Admin\Blocks\Navbar;

class Item extends \Magelight\Block
{
    protected $_template = 'Magelight/Admin/templates/navbar/item.phtml';

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

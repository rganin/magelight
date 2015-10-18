<?php
namespace Magelight\Admin\Blocks\Sections;

class Item extends \Magelight\Block
{
    /**
     * @var string
     */
    protected $template = 'Magelight/Admin/templates/sections/item.phtml';

    /**
     * Forgery constructor
     *
     * @param \SimpleXMLElement $itemConfig
     */
    public function __forge(\SimpleXMLElement $itemConfig)
    {
        $this->title = (string)$itemConfig->title;
        $this->type = (string)$itemConfig->type;
        $this->route = (string)$itemConfig->route;
        $this->class = (string)$itemConfig->class;
        $this->link_class = (string)$itemConfig->link_class;
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

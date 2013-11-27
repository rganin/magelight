<?php

namespace Magelight\Admin\Blocks\Navbar\Item;

use Magelight\Exception;

class Forgery
{
    use \Magelight\Traits\TForgery;

    /**
     * Default block class for navbar item
     */
    const DEFAULT_ITEM_BLOCK_CLASS = 'Magelight\Admin\Blocks\Navbar\Item';

    /**
     * @param \SimpleXMLElement $config
     *
     * @return Magelight\Admin\Blocks\Navbar\Item
     * @throws \Magelight\Exception
     */
    public function getForgedItem(\SimpleXMLElement $config, \Magelight\Admin\Blocks\Navbar\Item $parentItem = null)
    {
        $itemBlockClass = (string)$config->block;
        if (empty($itemBlockClass)) {
            $itemBlockClass = static::DEFAULT_ITEM_BLOCK_CLASS;
        } else {
            if (!class_exists($itemBlockClass)) {
                throw new \Magelight\Exception("Amin navbar element's class $itemBlockClass does not exist");
            }
        }
        return self::callStaticLate([$itemBlockClass, 'forge'], [$config, $parentItem]);
    }

    /**
     * Get set of items forged
     *
     * @param \SimpleXMLElement $config
     * @param \Magelight\Admin\Blocks\Navbar\Item $parentItem
     *
     * @return array
     */
    public function forgeItems(\SimpleXMLElement $config, \Magelight\Admin\Blocks\Navbar\Item $parentItem = null)
    {
        $items = [];
        foreach ($config->children() as $item) {
            /** @var $item \SimpleXMLElement */
            $items[(int)$item->position] = $this->getForgedItem($item, $parentItem);
        }
        ksort($items);
        return $items;
    }
}

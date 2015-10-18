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

namespace Magelight\Admin\Blocks\Navbar\Item;

/**
 * Class Forgery
 * @package Magelight\Admin\Blocks\Navbar\Item
 */
class Forgery
{
    use \Magelight\Traits\TForgery;

    /**
     * Default block class for navbar item
     */
    const DEFAULT_ITEM_BLOCK_CLASS = 'Magelight\Admin\Blocks\Navbar\Item';

    /**
     * @param \SimpleXMLElement $config
     * @param \Magelight\Admin\Blocks\Navbar\Item|null $parentItem
     *
     * @return \Magelight\Admin\Blocks\Navbar\Item
     * @throws \Magelight\Exception
     */
    public function getForgedItem(\SimpleXMLElement $config, \Magelight\Admin\Blocks\Navbar\Item $parentItem = null)
    {
        $itemBlockClass = (string)$config->block;
        if (empty($itemBlockClass)) {
            $itemBlockClass = static::DEFAULT_ITEM_BLOCK_CLASS;
        } else {
            if (!class_exists($itemBlockClass)) {
                throw new \Magelight\Exception(
                    __("Amin navbar element's class %s does not exist", $itemBlockClass)
                );
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

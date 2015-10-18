<?php

namespace Magelight\Admin\Blocks\Sections\Item;

class Forgery
{
    use \Magelight\Traits\TForgery;

    /**
     * Default block class for navbar item
     */
    const DEFAULT_ITEM_BLOCK_CLASS = 'Magelight\Admin\Blocks\Sections\Item';

    /**
     * @param \SimpleXMLElement $config
     *
     * @return \Magelight\Admin\Blocks\Sections\Item
     * @throws \Magelight\Exception
     */
    public function getForgedItem(\SimpleXMLElement $config)
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
        return self::callStaticLate([$itemBlockClass, 'forge'], [$config]);
    }

    /**
     * Get set of items forged
     *
     * @param \SimpleXMLElement $config
     *
     * @return array
     */
    public function forgeItems(\SimpleXMLElement $config)
    {
        $items = [];
        foreach ($config->children() as $item) {
            /** @var $item \SimpleXMLElement */
            $items[(int)$item->position] = $this->getForgedItem($item);
        }
        ksort($items);
        return $items;
    }
}

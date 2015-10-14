<?php

namespace Magelight\Cache;

use Magelight\Traits\TForgery;

/**
 * Class AdapterPool
 * @package Magelight\Cache
 *
 * @method static AdapterPool getInstance()
 */
class AdapterPool
{
    use TForgery;

    /**
     * Get adapter class by type string
     *
     * @param string $type
     * @return string
     */
    public function getAdapterClassByType($type)
    {
        return '\\Magelight\\Cache\\Adapter\\' . ucfirst(strtolower($type));
    }

    /**
     * Get adapter by index
     *
     * @param string $index
     * @return \Magelight\Cache\AdapterAbstract
     */
    public function getAdapter($index = \Magelight\App::DEFAULT_INDEX)
    {
        $config = \Magelight\Config::getInstance()->getConfig('global/cache/' . $index);
        $type = $this->getAdapterClassByType((bool)$config->disabled ? 'dummy' : $config->type);
        return call_user_func_array([$type, 'getInstance'], [$config->config]);
    }
}

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

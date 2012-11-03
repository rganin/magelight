<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 03.11.12
 * Time: 14:18
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Dbal\Db;

abstract class AbstractAdapter
{
    /**
     * Initialized flag
     *
     * @var bool
     */
    protected $_isInitialized = false;

    /**
     * Initialize DB instance
     *
     * @param array $options
     * @return mixed
     */
    abstract public function init(array $options = []);

    /**
     * Check the DB is initialized
     *
     * @return bool
     */
    public function isInitialized()
    {
        return $this->_isInitialized;
    }

    /**
     * Get adapter class by type
     *
     * @param string $type
     * @return string
     */
    public static function getAdapterClassByType($type)
    {
        return '\\Magelight\\Dbal\\Db\\' . ucfirst(strtolower($type)) . '\\Adapter';
    }
}
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
 * @copyright Copyright (c) 2012 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight\Dbal\Db\Common;

abstract class Adapter
{
    use \Magelight\Forgery;

    const TYPE_MYSQL = 'mysql';
    const TYPE_PGSQL = 'pgsql';
    const TYPE_OCI   = 'oci';
    const TYPE_MSSQL = 'mssql';
    const TYPE_MONGO = 'mongodb';
    /**
     * Initialized flag
     *
     * @var bool
     */
    protected $_isInitialized = false;

    /**
     * Adapter type
     *
     * @var string
     */
    protected $_type = self::TYPE_MYSQL;

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


    /**
     * Forge an adapter
     *
     * @return Adapter
     */
    public static function forge()
    {
        return new static();
    }

    /**
     * Get adapter type
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }
}

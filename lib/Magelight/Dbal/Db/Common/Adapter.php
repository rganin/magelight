<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 03.11.12
 * Time: 14:18
 * To change this template use File | Settings | File Templates.
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

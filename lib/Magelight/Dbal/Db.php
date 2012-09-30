<?php

namespace Magelight\Dbal;

/**
 *
 * Enter description here ...
 * @author iddqd
 *
 * @method \PDOStatement prepare() prepare($query) - prepare PDO statement with query
 * @method bool beginTransaction() - begin the transaction
 * @method bool commit() - commit the transaction
 * @method \PDOStatement query()
 * @method bool rollBack() - roll back the transaction
 * @method int exec() - execute sql statement
 */
class Db{

    /**
     * Database connection object
     *
     * @var \PDO
     */
    private static $db = null;

    /**
     * Making constructor private for singleton
     */
    final private function __construct()
    {
    }

    /**
     * Initialize database PDO singleton with config auth data
     */
    public static function init($dbConnectionPath = null, $dbUser = null, $dbPass = null)
    {

        self::$db = new \PDO(
        					!empty($dbConnectionPath) ? $dbConnectionPath : '',
        					!empty($dbUser) ? $dbUser : '',
        					!empty($dbPass) ? $dbPass : '');

    }

    /**
     * Get instance of database object
     *
     * @return \PDO
     *
     */
    public static function &getInstance()
    {
        if (!self::$db instanceof \PDO) {
            self::init();
        }
        return self::$db;
    }

    /**
     * Call static magic method
     * @param string $method
     * @param array $arguments
     *
     * @return mixed
     */
    public static function __callStatic($method, $arguments)
    {
        $db = self::getInstance();
        if(!empty($db) && $db instanceof \PDO){
            return call_user_func_array(array($db, $method), $arguments);
        };
    }
}
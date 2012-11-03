<?php

namespace Magelight\Dbal\Db\MySql;

/**
 * Mysql adapter (PDO proxy)
 *
 * @method \PDOStatement prepare($query) - prepare PDO statement with query
 * @method bool beginTransaction() - begin the transaction
 * @method bool commit() - commit the transaction
 * @method \PDOStatement query()
 * @method bool rollBack() - roll back the transaction
 * @method int exec($pdoStatement) - execute sql statement
 */
class Adapter extends \Magelight\Dbal\Db\AbstractAdapter{

    /**
     * PDO Object
     *
     * @var \PDO|null
     */
    protected $_db = null;

    /**
     * Initialize DB adapter
     *
     * @param array $options
     * @return Adapter|mixed
     */
    public function init(array $options = [])
    {
        $dsn = isset($options['dsn']) ? $options['dsn'] : $this->getDsn($options);

        $user = isset($options['user']) ? $options['user'] : null;
        $pass = isset($options['password']) ? $options['password'] : null;
        $this->_db = new \PDO($dsn, $user, $pass, $this->preparePdoOptions($options));

        if ($this->_db instanceof \PDO) {
            $this->_isInitialized = true;
        }

        if (isset($config['use_database'])) {
            $this->_db->exec('USE ' . $config['database']);
        }

        return $this;
    }

    /**
     * Get DSN from options
     *
     * @param array $options
     * @return string
     */
    protected function getDsn(array $options)
    {
        $dsn = $options['type'] . ':';
        $dsnParams = [];
        foreach (['host', 'port', 'dbname', 'unix_socket', 'charset'] as $index) {
            if (isset($options[$index])) {
                $dsnParams[] = $index . '=' . $options[$index];
            }
        }
        return $dsn . implode(';', $dsnParams);
    }


    /**
     * Prepare PDO options
     *
     * @param array $options
     * @return array
     */
    protected function preparePdoOptions(array $options = [])
    {
        $pdoOptions = [];
        if (isset($options['init_connect'])) {
            $pdoOptions[\PDO::MYSQL_ATTR_INIT_COMMAND] = (string) $options['init_connect'];
        }
        if (isset($options['compression'])) {
            $pdoOptions[\PDO::MYSQL_ATTR_COMPRESS] = (int) $options['compression'];
        }
        if (isset($options['compression'])) {
            $pdoOptions[\PDO::ATTR_PERSISTENT] = (int) $options['persistent'];
        }
        return $pdoOptions;
    }

    /**
     * Call magic method
     *
     * @param string $method
     * @param array $arguments
     * @throws \Magelight\Exception
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if($this->_db instanceof \PDO){
            return call_user_func_array(array($this->_db, $method), $arguments);
        } else {
            throw new \Magelight\Exception('Database adapter PDO object is missing or invalid.');
        }
    }
}

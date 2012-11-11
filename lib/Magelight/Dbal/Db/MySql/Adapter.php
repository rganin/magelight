<?php

namespace Magelight\Dbal\Db\MySql;

/**
 * Mysql adapter (PDO proxy)
 *
 * @method bool beginTransaction() - begin the transaction
 * @method bool commit() - commit the transaction
 * @method bool rollBack() - roll back the transaction
 * @method int exec($pdoStatement) - execute sql statement
 */
class Adapter extends \Magelight\Dbal\Db\Common\Adapter
{

    /**
     * PDO Object
     *
     * @var \PDO|null
     */
    protected $_db = null;

    /**
     * DSN Address
     *
     * @var string
     */
    protected $_dsn = '';

    /**
     * Adapter type
     *
     * @var string
     */
    protected $_type = self::TYPE_MYSQL;

    /**
     * Profiling enabled
     *
     * @var bool
     */
    protected $_profilingEnabled = false;

    /**
     * Initialize adapter
     *
     * @param array $options
     * @return Adapter|mixed
     */
    public function init(array $options = [])
    {
        $this->_dsn = isset($options['dsn']) ? $options['dsn'] : $this->getDsn($options);

        $user = isset($options['user']) ? $options['user'] : null;
        $pass = isset($options['password']) ? $options['password'] : null;
        $this->_db = new \PDO($this->_dsn, $user, $pass, $this->preparePdoOptions($options));

        if ($this->_db instanceof \PDO) {
            $this->_isInitialized = true;
        }

        if (isset($options['use_database'])) {
            $this->_db->exec('USE ' . $options['database']);
        }

        if (isset($options['profiling']) && (bool) $options['profiling']) {
            $this->enableProfilig();
        }

        return $this;
    }

    /**
     * Enable profiling
     *
     * @return Adapter
     */
    public function enableProfilig()
    {
        $this->_profilingEnabled = true;
        return $this;
    }

    /**
     * Disable profiling
     *
     * @return Adapter
     */
    public function disableProfiling()
    {
        $this->_profilingEnabled = false;
        return $this;
    }

    /**
     * Get db profile
     *
     * @return array
     */
    public function getProfile()
    {
        return \Magelight\Profiler::getInstance($this->_dsn)->getProfile();
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

    /**
     * Execute db statement
     *
     * @param string $query
     * @param array $params
     * @return \PDOStatement
     * @throws \Magelight\Exception
     */
    public function execute($query, $params = [])
    {
        $statement = $this->_db->prepare($query);
        /* @var $statement \PDOStatement*/
        if ($this->_profilingEnabled) {
            $profiler = \Magelight\Profiler::getInstance($this->_dsn);
            /* @var $profiler \Magelight\Profiler */
            $profileId = $profiler->startNewProfiling();
        }

        if (!$statement->execute(array_values($params))) {
            $errorInfo = $statement->errorInfo();
            trigger_error(
                "Adapter error: `" . $errorInfo . '`'
                . 'statement = "' . $statement->queryString . '"'
                . 'params = ' . var_export($params, true)
                , E_USER_NOTICE);
        }

        if ($this->_profilingEnabled) {
            $data = ['query' => $statement->queryString];
            if (isset($errorInfo)) {
                $data['error'] = $errorInfo;

            }
            $profiler->finish($profileId, $data);
        }
        return $statement;
    }
}

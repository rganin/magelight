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

namespace Magelight\Db\Common;
use Magelight\Exception;

/**
 * Abstract adapter
 */
abstract class Adapter
{
    /**
     * Use forgery
     */
    use \Magelight\Traits\TForgery;

    /**
     * Database type constants
     */
    const TYPE_MYSQL  = 'mysql';
    const TYPE_PGSQL  = 'pgsql';
    const TYPE_OCI    = 'oci';
    const TYPE_MSSQL  = 'mssql';
    const TYPE_SQLITE = 'sqlite';

    /**
     * PDO Object
     *
     * @var \PDO|null
     */
    protected $pdo = null;

    /**
     * DSN Address
     *
     * @var string
     */
    protected $dsn = '';

    /**
     * Params
     *
     * @var array
     */
    protected $params = [];

    /**
     * Adapter type
     *
     * @var string
     */
    protected $type = self::TYPE_MYSQL;

    /**
     * Profiling enabled
     *
     * @var bool
     */
    protected $profilingEnabled = false;

    /**
     * Initialized flag
     *
     * @var bool
     */
    protected $isInitialized = false;

    /**
     * Level of transaction
     *
     * @var int
     */
    protected $transactionLevel = 0;

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
        return $this->isInitialized;
    }

    /**
     * Get adapter class by type
     *
     * @param string $type
     * @return string
     */
    public static function getAdapterClassByType($type)
    {
        return '\\Magelight\\Db\\' . ucfirst(strtolower($type)) . '\\Adapter';
    }

    /**
     * Get adapter type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Enable profiling
     *
     * @return Adapter
     */
    public function enableProfilig()
    {
        $this->profilingEnabled = true;
        return $this;
    }

    /**
     * Disable profiling
     *
     * @return Adapter
     */
    public function disableProfiling()
    {
        $this->profilingEnabled = false;
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
        $this->params = $options;
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
     * Prepare unique db server trigger or foreign key name
     *
     * @param string $name
     * @return string
     */
    public function prepareUniqueTriggerName($name)
    {
        return isset($this->params['dbname']) ?
            ($this->params['dbname'] . '_' . $name) :
            (md5(time() . md5($this->params))) . '_' . $name;
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
        $profileId = null;
        /* @var $statement \PDOStatement*/
        $statement = $this->pdo->prepare($query);
        if ($this->profilingEnabled) {
            $profileId = $this->getProfiler()->startNewProfiling();
        }

        if (!$statement) {
            $params = var_export($params, true);
            $query = substr($query, 0, 1024) . '...';
            $dbError = var_export($this->pdo->errorInfo(), true);
            throw new \Magelight\Exception(
                "Error preparing statement:\r\n {$query}\r\n params = {$params}\r\n Database error: {$dbError}"
            );
        }
        if (!$statement->execute($params)) {
            $errorInfo = $statement->errorInfo();
            trigger_error(
                "Adapter error: `" . var_export($errorInfo, true) . '`'
                    . 'statement = "' . $statement->queryString . '"'
                    . 'params = ' . var_export($params, true)
                , E_USER_NOTICE);
        }

        if ($this->profilingEnabled) {
            $data = ['query' => $statement->queryString];
            if (isset($errorInfo)) {
                $data['error'] = $errorInfo;

            }
            if ($this->profilingEnabled) {
                $this->getProfiler()->finish($profileId, $data);
            }
        }
        return $statement;
    }

    /**
     * Get PDO param type
     *
     * @param string $param
     * @return int
     */
    protected function getParamType($param)
    {
        if (is_null($param)) {
            return \PDO::PARAM_NULL;
        } elseif (is_int($param)) {
            return \PDO::PARAM_INT;
        } elseif (is_bool($param)) {
            return \PDO::PARAM_BOOL;
        }
        return \PDO::PARAM_STR;
    }

    /**
     * Get profiler
     *
     * @return \Magelight\Profiler
     */
    public function getProfiler()
    {
        return \Magelight\Profiler::getInstance($this->dsn);
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
        if($this->pdo instanceof \PDO){
            return call_user_func_array(array($this->pdo, $method), $arguments);
        } else {
            throw new \Magelight\Exception('Database adapter PDO object is missing or invalid.');
        }
    }

    /**
     * Begin transaction or increase level
     *
     * @return bool
     */
    public function beginTransaction()
    {
        // if transaction is not started
        if ($this->transactionLevel === 0) {
            // and start was successful
            if ( $this->pdo->beginTransaction()) {
                // increase transaction level
                $this->transactionLevel += 1;
                return true;
            } else {
                return false;
            }
        // if transaction was already started
        } else {
            // just increase transaction level
            $this->transactionLevel += 1;
            return true;
        }
    }

    /**
     * Commit transaction or decrease level
     *
     * @return bool
     *
     * @throws Exception
     */
    public function commitTransaction()
    {
        $this->transactionLevel -= 1;
        if ($this->transactionLevel === 0) {
            if (!$this->pdo->inTransaction()) {
                throw new Exception('Asymmetric transaction commit attempt');
            }
            return $this->pdo->commit();
        }
        return true;
    }

    /**
     * Roll back the transaction or decrease level
     *
     * @return bool
     *
     * @throws Exception
     */
    public function rollbackTransaction()
    {
        $this->transactionLevel -= 1;
        if ($this->transactionLevel === 0) {
            if (!$this->pdo->inTransaction()) {
                throw new Exception('Asymmetric transaction rollback attempt');
            }
            return $this->pdo->rollBack();
        }
        return true;
    }
}

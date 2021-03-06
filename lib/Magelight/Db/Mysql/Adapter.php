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

namespace Magelight\Db\Mysql;

/**
 * Mysql adapter (PDO proxy)
 */
class Adapter extends \Magelight\Db\Common\Adapter
{

    /**
     * Initialize adapter
     *
     * @param array $options
     * @return Adapter|mixed
     */
    public function init(array $options = [])
    {
        $this->dsn = isset($options['dsn']) ? $options['dsn'] : $this->getDsn($options);

        $user = isset($options['user']) ? $options['user'] : null;
        $pass = isset($options['password']) ? $options['password'] : null;

        $this->pdo = new \PDO($this->dsn, $user, $pass, $this->preparePdoOptions($options));

        if (isset($options['use_database'])) {
            $this->pdo->exec('USE ' . $options['database']);
        }

        if (isset($options['profiling']) && (bool) $options['profiling']) {
            $this->enableProfilig();
        }

        $this->isInitialized = true;
        return $this;
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
        if (isset($options['persistent'])) {
            $pdoOptions[\PDO::ATTR_PERSISTENT] = (int) $options['persistent'];
        }
        return $pdoOptions;
    }
}

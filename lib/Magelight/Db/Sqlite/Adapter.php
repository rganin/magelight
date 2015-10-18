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

namespace Magelight\Db\Sqlite;

/**
 * Adapter for Sqlite database
 */
class Adapter extends \Magelight\Db\Common\Adapter
{
    /**
     * @var string
     */
    protected $type = self::TYPE_SQLITE;

    /**
     * Initialize DB instance
     *
     * @param array $options
     * @return mixed
     */
    public function init(array $options = [])
    {
        $this->dsn = isset($options['dsn']) ? $options['dsn'] : $this->getDsn($options);
        $this->pdo = new \PDO($this->dsn, null, null, $this->preparePdoOptions($options));
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
        if (isset($options['persistent'])) {
            $pdoOptions[\PDO::ATTR_PERSISTENT] = (int) $options['persistent'];
        }
        return $pdoOptions;
    }
}

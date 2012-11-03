<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 03.11.12
 * Time: 17:42
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Dbal\Db;

abstract class AbstractOrm
{
    protected $db = null;

    protected $idColumn = 'id';

    protected $data = [];

    /**
     * Cache key for orm instance
     *
     * @var string
     */
    protected $cacheKey = null;

    /**
     * Default cache time (seconds)
     *
     * @var int
     */
    protected $cacheTime = 60;

    /**
     * Is cache enabled
     *
     * @var bool
     */
    protected $cacheEnabled = false;

    public function __construct(\Magelight\Dbal\Db\AbstractAdapter $db)
    {
        $this->db = $db;
    }

    public function setData(array $data = [])
    {

    }
}
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
 * @copyright Copyright (c) 2013 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight\Visitors\Models;

/**
 * Class Visitor
 * @package Magelight\Visitors\Models
 *
 * @method static $this forge($data = [], $forceNew = false)
 */
class Visitor extends \Magelight\Model
{
    /**
     * Date time format for front
     */
    const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * Table name
     *
     * @var string
     */
    protected static $tableName = 'visitors';

    /**
     * Encount visitor
     *
     * @param string $requestRoute
     *
     * @return Visitor
     */
    public function encount($requestRoute)
    {
        $time = time();
        $todayStart = $time - ($time % 86400);
        $todayEnd = $todayStart + 86400;
        $ipLong = ip2long(isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 0);
        $current = $this->orm()
            ->whereGt('time', $todayStart)
            ->whereLt('time', $todayEnd)
            ->whereEq('ip', $ipLong)
            ->fetchModel();
        if ($current instanceof self) {
            $current->time = $time;
            $info = json_decode(gzuncompress($current->info), true);
            $info[$requestRoute] = isset($info[$requestRoute]) ? $info[$requestRoute] + 1 : 1;
            $current->info = gzcompress(json_encode($info), 8);
            unset($info);
            $current->save(false);
            unset($current);
        } else {
            $this->time = $time;
            $this->ip = $ipLong;
            $this->referer = \Magelight\Http\Server::getInstance()->getHttpReferer('direct');
            $this->info = gzcompress(json_encode([
                $requestRoute => 1
            ]), 8);
            $this->save(true);
        }
        return $this;
    }

    /**
     * After load handler
     *
     * @return \Magelight\Model
     */
    public function afterLoad()
    {
        $this->time = date(self::DATE_TIME_FORMAT, $this->time);
        $this->ip = long2ip($this->ip);
        return parent::afterLoad();
    }

    /**
     * Before save handler
     *
     * @return \Magelight\Model|void
     */
    public function beforeSave()
    {
        if (!is_int($this->time)) {
            $this->time = strtotime($this->time);
        }
        if (!is_int($this->ip)) {
            $this->ip = ip2long($this->ip);
        }
        return parent::beforeSave();
    }
}

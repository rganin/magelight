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

namespace Magelight;

/**
 * Profiler class
 */
class Profiler
{
    /**
     * Profiler instances
     *
     * @var array
     */
    protected static $instances = [];

    /**
     * Profiles array
     *
     * @var array
     */
    protected $profiles = [];

    /**
     * Get profiler instance
     *
     * @param string $type
     * @return mixed
     */
    public static function getInstance($type)
    {
        if (!isset(self::$instances[$type])) {
            self::$instances[$type] = new static();
        }
        return self::$instances[$type];
    }

    /**
     * Start new profiling thread, returns thread index
     *
     * @return int
     */
    public function startNewProfiling()
    {
        $profile = [
            'start' => microtime(true),
        ];
        $index = count($this->profiles);
        $this->profiles[] = $profile;
        return $index;
    }

    /**
     * Finish profling thread
     *
     * @param int $threadId
     * @param array $data
     */
    public function finish($threadId, $data = [])
    {
        $this->profiles[$threadId]['end'] = microtime(true);
        $this->profiles[$threadId]['sec']
            = $this->profiles[$threadId]['end'] - $this->profiles[$threadId]['start'];
        $this->profiles[$threadId]['data'] = $data;
    }

    /**
     * Get profiles
     *
     * @return array
     */
    public function getProfile()
    {
        return $this->profiles;
    }
}

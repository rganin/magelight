<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 10.11.12
 * Time: 15:25
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight;

class Profiler
{
    /**
     * Profiler instances
     *
     * @var array
     */
    protected static $_instances = [];

    protected $_profiles = [];

    public static function getInstance($type)
    {
        if (!isset(self::$_instances[$type])) {
            self::$_instances[$type] = new static();
        }
        return self::$_instances[$type];
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
        $index = count($this->_profiles);
        $this->_profiles[] = $profile;
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
        $this->_profiles[$threadId]['end'] = microtime(true);
        $this->_profiles[$threadId]['sec']
            = $this->_profiles[$threadId]['end'] - $this->_profiles[$threadId]['start'];
        $this->_profiles[$threadId]['data'] = $data;
    }

    /**
     * Get profiles
     *
     * @return array
     */
    public function getProfile()
    {
        return $this->_profiles;
    }
}

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

namespace Magelight\Forgery;

/**
 * Class MockContainer
 * @package Magelight\Forgery
 */
class MockContainer
{
    /**
     * Mock objects stack
     *
     * @var array
     */
    protected static $mockObjects = [];

    /**
     * Forgery calls by class
     *
     * @var array
     */
    protected static $calls = [];

    /**
     * Add mock object to container
     *
     * @param string $calledClass
     * @param \PHPUnit_Framework_MockObject_MockObject $mockObject
     * @param int|null $iteration
     */
    public function addMockObject(
        $calledClass,
        \PHPUnit_Framework_MockObject_MockObject $mockObject,
        $iteration = null
    ) {
        self::$mockObjects[$calledClass][$iteration] = $mockObject;
    }

    public function getMock($className)
    {
        if (isset(self::$calls[$className])) {
            self::$calls[$className]++;
        } else {
            self::$calls[$className] = 1;
        }
        if (isset(self::$mockObjects[$className][self::$calls[$className]])) {
            return self::$mockObjects[$className][self::$calls[$className]];
        }
        if (isset(self::$mockObjects[$className][null])) {
            return self::$mockObjects[$className][null];
        }
        return null;
    }

    /**
     * Reset mock container
     */
    public function reset()
    {
        self::$mockObjects = [];
    }

    /**
     * Get container instance
     *
     * @return $this
     */
    public static function getInstance()
    {
        static $instance;
        if (!$instance instanceof static) {
            $instance = new static();
        }
        return $instance;
    }

    /**
     * Closing constructor
     */
    protected function __construct()
    {

    }
}

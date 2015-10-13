<?php
namespace Magelight\Forgery;

use Magelight\Exception;

class MockContainer
{
    protected static $mockObjects = [];


    /**
     * Forgery calls by class
     *
     * @var array
     */
    protected static $calls = [];

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

    public function reset()
    {
        self::$mockObjects = [];
    }

    public static function getInstance()
    {
        static $instance;
        if (!$instance instanceof static) {
            $instance = new static();
        }
        return $instance;
    }

    protected function __construct()
    {

    }
}

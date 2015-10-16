<?php

namespace Magelight;

class ForgeryTest extends \Magelight\TestCase
{
    /**
     * @var \Magelight\Forgery
     */
    protected $forgery;

    public function setUp()
    {
        $this->forgery = \Magelight\Forgery::getInstance();
    }

    public function testSetPreference()
    {
        $class = 'Magelight\Class1';
        $preference = 'Magelight\Class2';
        $overPreference = 'Magelight\Class3';
        $this->assertEquals($class, $this->forgery->getClassName($class));
        $this->forgery->setPreference($class, $preference);
        $this->assertEquals($preference, $this->forgery->getClassName($class));
        $this->forgery->setPreference($preference, $overPreference);
        $this->assertEquals($overPreference, $this->forgery->getClassName($class));
        $this->assertEquals($overPreference, $this->forgery->getClassName($preference));
    }

    public function testOverrideInterfaces()
    {
        $class = 'Magelight\Class1';
        $interface1 = 'Magelight\Interface1';
        $interface2 = 'Magelight\Interface2';
        $this->assertEquals([], $this->forgery->getClassInterfaces($class));
        $this->forgery->addClassOverrideInterface($class, $interface1);
        $this->forgery->addClassOverrideInterface($class, $interface2);
        $this->assertEquals([$interface1, $interface2], $this->forgery->getClassInterfaces($class));
    }

    public function testLoadPreferences()
    {
        $config = new \SimpleXMLElement('<config><global><forgery>
            <preference>
                <old>Magelight\Blocks\Body</old>
                <new>App\Blocks\Body</new>
            </preference>
            <preference>
                <old>Magelight\Blocks\Header</old>
                <new>App\Blocks\Header</new>
            </preference>
            <preference>
                <old>Magelight\Blocks\Footer</old>
                <new>App\Blocks\Footer</new>
            </preference>
            <preference>
                <old>App\Blocks\Footer</old>
                <new>App\Blocks\FooterExtended</new>
                <interface>App\Blocks\FooterInterface</interface>
            </preference>
        </forgery></global></config>');
        $config = (array)$config->xpath('//config/global/forgery/preference');
        $this->forgery->loadPreferences($config);
        $this->assertEquals('App\Blocks\Header', $this->forgery->getClassName('Magelight\Blocks\Header'));
        $this->assertEquals('App\Blocks\Body', $this->forgery->getClassName('Magelight\Blocks\Body'));
        $this->assertEquals('App\Blocks\FooterExtended', $this->forgery->getClassName('Magelight\Blocks\Footer'));
        $this->assertEquals(
            ['App\Blocks\FooterInterface'],
            $this->forgery->getClassInterfaces('App\Blocks\FooterExtended')
        );
    }
}

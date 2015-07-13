<?php
namespace PhoneCom\Mason\Tests\Components;

class HashTest extends \PHPUnit_Framework_TestCase
{
    public function testCanInstantiate()
    {
        new ConcreteHash();
        $this->assertTrue(true);
    }

    public function testCanSetPropertyViaSetter()
    {
        $obj = new ConcreteHash();
        $obj->setProperty('tailgate', 'Purple');
        $this->assertEquals('Purple', $obj->tailgate);
    }

    public function testCanSetCustomProperty()
    {
        $obj = new ConcreteHash();
        $obj->setProperty('custom', 'This value');
        $this->assertEquals('This value', $obj->custom);
    }

}

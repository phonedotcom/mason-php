<?php
namespace PhoneCom\Mason\Tests;

use PhoneCom\Mason\Builder\Child;
use PhoneCom\Mason\Builder\Components\Control;

class ChildTest extends \PHPUnit_Framework_TestCase
{
    public function testCanInstantiate()
    {
        new Child();
        $this->assertTrue(true);
    }

    public function testCanSetControlsByObject()
    {
        $obj = new Child([
            '@controls' => [
                'related' => new Control('/path')
            ]
        ]);
        $this->assertEquals('/path', $obj->{'@controls'}->related->href);
    }

    public function testCanSetControlsByArray()
    {
        $obj = new Child([
            '@controls' => [
                'related' => ['href' => '/path']
            ]
        ]);
        $this->assertEquals('/path', $obj->{'@controls'}->related->href);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSettingInvalidControlFails()
    {
        new Child([
            '@controls' => [
                'related' => 'foo'
            ]
        ]);
    }
}

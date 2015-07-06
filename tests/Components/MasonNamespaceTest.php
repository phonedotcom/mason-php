<?php
namespace PhoneCom\Mason\Tests\Components;

use PhoneCom\Mason\Builder\Components\MasonNamespace;

class MasonNamespaceTest extends \PHPUnit_Framework_TestCase
{
    public function testCanInstantiate()
    {
        new MasonNamespace('http://example.com');
        $this->assertTrue(true);
    }

    public function testCanSetName()
    {
        $obj = new MasonNamespace('http://example.com');
        $obj->setName('http://google.com');
        $this->assertEquals('http://google.com', $obj->name);
    }

}

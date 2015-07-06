<?php
namespace PhoneCom\Mason\Tests\Components;

use PhoneCom\Mason\Builder\Components\MasonNamespace;
use PhoneCom\Mason\Builder\Components\Namespaces;

class NamespacesTest extends \PHPUnit_Framework_TestCase
{
    public function testCanInstantiate()
    {
        new Namespaces();
        $this->assertTrue(true);
    }

    public function testCanAddNamespace()
    {
        $obj = new Namespaces();
        $obj->addNamespace('is', new MasonNamespace('http://google.com'));
        $this->assertEquals('http://google.com', $obj->is->name);
    }

    public function testCanAddNamespaceByArray()
    {
        $obj = new Namespaces();
        $obj->addNamespace('is', 'http://example.com/is#');
        $this->assertEquals('http://example.com/is#', $obj->is->name);
    }

}

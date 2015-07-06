<?php
namespace PhoneCom\Mason\Tests\Components;

use PhoneCom\Mason\Builder\Components\Meta;

class MetaTest extends \PHPUnit_Framework_TestCase
{
    public function testCanInstantiate()
    {
        new Meta();
        $this->assertTrue(true);
    }

    public function testCanInstantiateWithProperties()
    {
        $obj = new Meta(['title' => 'My title', 'foo' => 'bar']);
        $this->assertEquals('bar', $obj->foo);
    }

    public function testCanSetTitle()
    {
        $this->runSetterTest('@title', 'Boom');
    }

    public function testCanSetDescription()
    {
        $this->runSetterTest('@description', 'Boom');
    }

    public function testCanAddControl()
    {
        $obj = new Meta();
        $obj->addControl('self', '/path/to/nowhere');
        $this->assertEquals('/path/to/nowhere', $obj->{'@controls'}->self->href);
    }

    public function testCanMinimize()
    {
        $obj = new Meta(['title' => 'Blip']);
        $obj->minimize();
        $this->assertObjectNotHasAttribute('title', $obj);
    }

    public function testCanProtectProperties()
    {
        $obj = new Meta();
        $obj->setProperty('@title', 'Blip', true);
        $obj->minimize();
        $this->assertEquals('Blip', $obj->{'@title'});
    }

    public function testCanProtectControls()
    {
        $obj = new Meta();
        $obj->addControl('self', '/over/the/rainbow', [], true);
        $obj->addControl('up', '/over/the');
        $obj->minimize();
        $this->assertObjectNotHasAttribute('up', $obj->{'@controls'});
        $this->assertEquals('/over/the/rainbow', $obj->{'@controls'}->self->href);
    }

    public function testRemovingAllControlsRemovesContainer()
    {
        $obj = new Meta();
        $obj->addControl('self', '/over/the/rainbow');
        $obj->minimize();
        $this->assertObjectNotHasAttribute('@controls', $obj);
    }

    private function runSetterTest($property, $value)
    {
        $obj = new Meta([$property => $value]);
        $this->assertEquals($value, $obj->{$property}, sprintf('Failed setting or retrieving "%s"', $property));
    }
}

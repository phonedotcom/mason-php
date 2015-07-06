<?php
namespace PhoneCom\Mason\Tests\Components;

use PhoneCom\Mason\Builder\Components\File;

class FileTest extends \PHPUnit_Framework_TestCase
{
    public function testCanInstantiate()
    {
        $obj = new File('audio', ['audio/mp3'], 'The Song', 'The song you want to upload');
        $this->assertEquals('audio', $obj->name);
        $this->assertEquals(['audio/mp3'], $obj->accept);
        $this->assertEquals('The Song', $obj->title);
        $this->assertEquals('The song you want to upload', $obj->description);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSettingCustomPropertyFails()
    {
        $obj = new File('audio');
        $obj->splat = 'messy';
    }

    public function testCanSetName()
    {
        $this->runSetterTest('name', 'Superman');
    }

    public function testCanSetTitle()
    {
        $this->runSetterTest('title', 'Boom');
    }

    public function testCanSetDescription()
    {
        $this->runSetterTest('description', 'Boom');
    }

    public function testCanSetAccept()
    {
        $this->runSetterTest('accept', ['text/plain', 'application/json']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidAcceptFails()
    {
        $obj = new File('avatar');
        $obj->setAccept(['yip', new \stdClass]);
    }

    public function testMinimizingRemovesTitle()
    {
        $obj = new File('file', [], 'My Title');
        $obj->minimize();

        $this->assertObjectNotHasAttribute('title', $obj);
    }

    public function testMinimizingRemovesDescription()
    {
        $obj = new File('file', [], 'foo', 'My Description');
        $obj->minimize();

        $this->assertObjectNotHasAttribute('description', $obj);
    }

    private function runSetterTest($property, $value)
    {
        $obj = new File('booky');
        $obj->setProperty($property, $value);
        $this->assertEquals($value, $obj->{$property}, sprintf('Failed setting or retrieving "%s"', $property));
    }

}

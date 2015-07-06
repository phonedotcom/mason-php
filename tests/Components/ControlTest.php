<?php
namespace PhoneCom\Mason\Tests\Components;

use PhoneCom\Mason\Builder\Components\Control;
use PhoneCom\Mason\Builder\Components\File;

class ControlTest extends \PHPUnit_Framework_TestCase
{
    public function testCanInstantiate()
    {
        $obj = new Control('/path');
        $this->assertEquals('/path', $obj->href);
    }

    public function testCanInstantiateWithExtraProperties()
    {
        $obj = new Control('/path', [
            'title' => 'Take 5'
        ]);
        $this->assertEquals('Take 5', $obj->title);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSettingCustomPropertyFails()
    {
        $obj = new Control('/path');
        $obj->splat = 'messy';
    }

    public function testCanSetHref()
    {
        $this->runSetterTest('href', '/street/sesame');
    }

    public function testCanSetIsHrefTemplate()
    {
        $this->runSetterTest('isHrefTemplate', false);
        $this->runSetterTest('isHrefTemplate', true);
    }

    public function testCanSetTitle()
    {
        $this->runSetterTest('title', 'Boom');
    }

    public function testCanSetDescription()
    {
        $this->runSetterTest('description', 'Boom');
    }

    public function testCanSetSchema()
    {
        $this->runSetterTest('schema', (object)['hi' => 'there']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidSchemaFails()
    {
        $obj = new Control('/path');
        $obj->setSchema('fail!');
    }

    public function testCanSetSchemaUrl()
    {
        $this->runSetterTest('schemaUrl', '/boom');
    }

    public function testCanSetTemplate()
    {
        $this->runSetterTest('template', (object)['zoo' => 'San Diego']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidTemplateFails()
    {
        $obj = new Control('/path');
        $obj->setTemplate('not an object');
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
        $obj = new Control('/path');
        $obj->setAccept(['yip', new \stdClass]);
    }

    public function testCanSetOutput()
    {
        $this->runSetterTest('output', ['application/schema+json']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidOutputFails()
    {
        $obj = new Control('/path');
        $obj->setOutput(['hub', new \stdClass]);
    }

    public function testCanSetMethod()
    {
        $this->runSetterTest('method', 'DELETE');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidMethodFails()
    {
        $obj = new Control('/path');
        $obj->setMethod('FLUMP');
    }

    public function testCanSetEncoding()
    {
        $this->runSetterTest('encoding', 'json');
    }

    public function testCanSetFiles()
    {
        $this->runSetterTest('files', [new File('avatar')]);
    }

    public function testCanAddFileAsObject()
    {
        $value = new File('image');

        $obj = new Control('/path');
        $obj->addFile($value);

        $this->assertEquals($value, current($obj->files));
    }

    public function testCanAddFileAsArray()
    {
        $value = ['name' => 'image', 'title' => 'Your Photo'];

        $obj = new Control('/path');
        $obj->addFile($value);

        $this->assertEquals('image', $obj->files[0]->name);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidFileFails()
    {
        $obj = new Control('/path');
        $obj->addFile('cabinet');
    }

    public function testCanSetAlt()
    {
        $this->runSetterTest('alt', [new Control('/white-house')]);
    }

    public function testCanAddAltAsObject()
    {
        $value = new Control('/nowhere');

        $obj = new Control('/path');
        $obj->addAlt($value);

        $this->assertEquals($value, current($obj->alt));
    }

    public function testCanAddAltAsArray()
    {
        $value = ['href' => '/images', 'title' => 'Somewhere out there'];

        $obj = new Control('/path');
        $obj->addAlt($value);

        $this->assertEquals('/images', $obj->alt[0]->href);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidAltFails()
    {
        $obj = new Control('/path');
        $obj->addAlt('mars');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidEncodingFails()
    {
        $obj = new Control('/path');
        $obj->setEncoding('atom');
    }

    public function testMinimizingRemovesTitle()
    {
        $obj = new Control('/path', ['title' => 'Abe Lincoln Biography']);
        $obj->minimize();

        $this->assertObjectNotHasAttribute('title', $obj);
    }

    public function testMinimizingRemovesDescription()
    {
        $obj = new Control('/path', ['description' => 'The bio of Old Abe']);
        $obj->minimize();

        $this->assertObjectNotHasAttribute('description', $obj);
    }

    private function runSetterTest($property, $value)
    {
        $obj = new Control('/path');
        $obj->setProperty($property, $value);
        $this->assertEquals($value, $obj->{$property}, sprintf('Failed setting or retrieving "%s"', $property));
    }

}

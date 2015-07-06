<?php
namespace PhoneCom\Mason\Tests\Components;

use PhoneCom\Mason\Builder\Components\Error;

class ErrorTest extends \PHPUnit_Framework_TestCase
{
    public function testCanInstantiate()
    {
        $obj = new Error('It b0rken');
        $this->assertEquals('It b0rken', $obj->{'@message'});
    }

    public function testCanInstantiateWithExtraProperties()
    {
        $obj = new Error('/path', [
            'title' => 'It really b0rk'
        ]);
        $this->assertEquals('It really b0rk', $obj->title);
    }

    public function testCanSetCustomProperty()
    {
        $obj = new Error('It b0rken');
        $obj->splat = 'messy';
        $this->assertEquals('messy', $obj->splat);
    }

    public function testCanSetMessage()
    {
        $this->runSetterTest('@message', 'Yup');
    }

    public function testCanSetId()
    {
        $this->runSetterTest('@id', 'asfd97a-asdf4a-asdf6as6');
    }

    public function testCanSetDetails()
    {
        $this->runSetterTest('@details', 'Uh oh, spaghettios');
    }

    public function testCanSetCode()
    {
        $this->runSetterTest('@code', 'YIOOSFIE');
    }

    public function testCanSetHttpStatusCode()
    {
        $this->runSetterTest('@httpStatusCode', 409);
    }

    public function testCanSetTime()
    {
        $now = microtime(true);
        $obj = new Error('yap', [], $now);

        $this->assertEquals(floor($now), strtotime($obj->{'@time'}));
    }

    public function testCanSetMessages()
    {
        $this->runSetterTest('@messages', ['First', 'Second']);
    }

    private function runSetterTest($property, $value)
    {
        $obj = new Error('nope, not working');
        $obj->setProperty($property, $value);
        $this->assertEquals($value, $obj->{$property}, sprintf('Failed setting or retrieving "%s"', $property));
    }

}

<?php
namespace Phonedotcom\Mason\Tests\Components;

use Phonedotcom\Mason\Builder\Components\Control;
use Phonedotcom\Mason\Builder\Components\Controls;
use Phonedotcom\Mason\Builder\Components\File;

class ControlsTest extends \PHPUnit_Framework_TestCase
{
    public function testCanInstantiate()
    {
        new Controls();
        $this->assertTrue(true);
    }

    public function testCanAddControlObject()
    {
        $control = new Control('/path');
        $obj = new Controls;
        $obj->setControl('self', $control);
        $this->assertEquals($control, $obj->self);
    }

    public function testCanAddControlByArray()
    {
        $obj = new Controls;
        $obj->setControl('self', '/path', ['title' => 'Tofu']);

        $this->assertEquals('Tofu', $obj->self->title);
    }

    public function testCanRemoveControl()
    {
        $obj = new Controls;
        $obj->setControl('self', '/path', ['title' => 'Tofu']);

        $obj->remove('self');

        $this->assertObjectNotHasAttribute('self', $obj);
    }
}

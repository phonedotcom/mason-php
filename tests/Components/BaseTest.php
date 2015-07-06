<?php
namespace PhoneCom\Mason\Tests\Components;

class BaseTest extends \PHPUnit_Framework_TestCase
{
    public function testCanMinimize()
    {
        $obj = new ConcreteBase([
            'bananas' => [
                [1, 2, 3],
                new ConcreteBase(['thumb' => 'Green'])
            ],
            'monkey' => new ConcreteBase(['thumb' => 'Broken'])
        ]);
        $obj->minimize();

        $this->assertObjectNotHasAttribute('thumb', $obj->monkey);
        $this->assertObjectNotHasAttribute('thumb', $obj->bananas[1]);
    }

    public function testCanSort()
    {
        $obj = new ConcreteBase([
            'bananas' => ['red', 'green', [1, 2, 3], new ConcreteBase()],
            'thumb' => 'Huge',
            'monkey' => new ConcreteBase(['bananas' => [9, 10, 11], 'thumb' => 'Healed'])
        ]);

        $obj->sort(['thumb', 'monkey', '{data}', 'fred']);

        $this->assertEquals(['thumb', 'monkey', 'bananas'], array_keys(get_object_vars($obj)));
        $this->assertEquals(['thumb', 'bananas'], array_keys(get_object_vars($obj->monkey)));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSortingRequiresDataPlaceholder()
    {
        $obj = new ConcreteBase();
        $obj->sort(['thumb', 'monkey']);
    }

}

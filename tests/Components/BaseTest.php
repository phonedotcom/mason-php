<?php
namespace PhoneCom\Mason\Tests\Components;

use PhoneCom\Mason\Builder\Child;
use PhoneCom\Mason\Builder\Document;

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
        $obj = new Document([
            'city' => [57, 19, 84],
            'robot' => 'Huge',
            'alarm' => new Child()
        ]);
        $obj->sort(['robot', 'alarm', '{data}', 'city']);

        $this->assertEquals(['robot', 'alarm', 'city'], array_keys(get_object_vars($obj)));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testDefaultSortingRequiresDataPlaceholder()
    {
        $obj = new Document();
        $obj->sort(['robot', 'alarm']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testControlsSortingRequiresDataPlaceholder()
    {
        $obj = new Document();
        $obj->sort(['robot', 'alarm', '{data}'], ['self', 'profile']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testMetaSortingRequiresDataPlaceholder()
    {
        $obj = new Document();
        $obj->sort(['robot', 'alarm', '{data}'], ['self', 'profile', '{data}'], ['@description', '@title']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testErrorSortingRequiresDataPlaceholder()
    {
        $obj = new Document();
        $obj->sort(['robot', 'alarm', '{data}'], ['self', 'profile', '{data}'], ['@description', '@title', '{data}'], ['@id']);
    }

    public function testCanSortChildObject()
    {
        $obj = new Document([
            'alarm' => new Child(['city' => [9, 10, 11], 'robot' => 'Broken'])
        ]);
        $obj->sort(['robot', 'alarm', '{data}', 'city']);

        $this->assertEquals(['robot', 'city'], array_keys(get_object_vars($obj->alarm)));
    }

    public function testCanSortChildObjectsInArray()
    {
        $obj = new Document([
            'city' => [
                new Child(['alarm' => 'circle', 'robot' => 'red']),
                new Child(['alarm' => 'square', 'robot' => 'green']),
                new Child(['alarm' => 'triangle', 'robot' => 'blue'])
            ]
        ]);
        $obj->sort(['robot', 'alarm', '{data}']);

        foreach ($obj->city as $index => $arrayObject) {
            $this->assertEquals(['robot', 'alarm'], array_keys(get_object_vars($arrayObject)), "Index $index");
        }
    }

    public function testCanIndependentlySortControls()
    {
        $defaultSorting = ['robot', 'alarm', '{data}', 'city'];
        $controlsSorting = ['parent', 'self', '{data}'];

        $obj = (new Document([
                'city' => [57, 19, 84],
                'robot' => 'Huge',
                'alarm' => new Child()
            ]))
            ->addControl('self', '/joker/silliness')
            ->addControl('parent', '/joker')
            ->sort($defaultSorting, $controlsSorting);

        $this->assertEquals(['robot', 'alarm', '@controls', 'city'], array_keys(get_object_vars($obj)));
        $this->assertEquals(['parent', 'self'], array_keys(get_object_vars($obj->{'@controls'})));
    }

    public function testCanIndependentlySortMeta()
    {
        $defaultSorting = ['robot', 'alarm', '{data}', 'city'];
        $metaSorting = ['@description', '{data}', '@title'];

        $obj = (new Document([
            'city' => [57, 19, 84],
            'robot' => 'Huge',
            'alarm' => new Child()
        ]))
            ->addMetaProperties([
                '@title' => 'Goodnight Moon',
                '@description' => 'Howling at the wind',
                'page_number' => 4
            ])
            ->sort($defaultSorting, null, $metaSorting);

        $this->assertEquals(['robot', 'alarm', '@meta', 'city'], array_keys(get_object_vars($obj)));
        $this->assertEquals(['@description', 'page_number', '@title'], array_keys(get_object_vars($obj->{'@meta'})));
    }

    public function testCanIndependentlySortError()
    {
        $defaultSorting = ['robot', 'alarm', '{data}', 'city'];
        $errorSorting = ['@id', '@code', '@httpStatusCode', '{data}'];

        $obj = (new Document([
            'city' => [57, 19, 84],
            'robot' => 'Huge',
            'alarm' => new Child()
        ]))
            ->setError('Your tie is crooked', [
                '@httpStatusCode' => 422,
                'granny' => 'freaked out',
                '@id' => 'sk9w-of7a-7wff',
                '@code' => 123,
            ])
            ->sort($defaultSorting, null, null, $errorSorting);

        $this->assertEquals(['robot', 'alarm', '@error', 'city'], array_keys(get_object_vars($obj)));
        $this->assertEquals(
            ['@id', '@code', '@httpStatusCode', '@message', 'granny', '@time'],
            array_keys(get_object_vars($obj->{'@error'}))
        );
    }

    public function testCanSortNestedArray()
    {
        $obj = (new Document(['favorites' => [[
                new Child(['food' => 'pepperoni pizza', 'drink' => 'orange juice']),
                new Child(['food' => 'bean and cheese burrito', 'drink' => 'milk']),
                new Child(['food' => 'drunken noodles', 'drink' => 'thai iced tea']),
            ]]]))
            ->sort(['drink', '{data}']);

        foreach ($obj->favorites[0] as $index => $child) {
            $this->assertEquals(['drink', 'food'], array_keys(get_object_vars($child)), "Index $index");
        }
    }

}

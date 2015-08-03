<?php
namespace Tests\Schemas;

use Tests\AssertionsTrait;
use Tests\TestCase;

class MasonBaseSchemaTest extends \PHPUnit_Framework_TestCase
{
    use AssertionsTrait;

    public function testCanSetDefaultSorting()
    {
        $data = json_decode(file_get_contents(__DIR__ . '/../../src/Schema/draft-2.json'));
        $schema = json_decode(file_get_contents(__DIR__ . '/../data/json-schema/draft-04/schema'));

        $this->assertValidSchema($data, $schema);
    }
}

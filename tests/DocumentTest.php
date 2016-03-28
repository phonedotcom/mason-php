<?php
namespace Phonedotcom\Mason\Tests;

use Phonedotcom\Mason\Builder\Child;
use Phonedotcom\Mason\Builder\Components\MasonNamespace;
use Phonedotcom\Mason\Builder\Document;

class DocumentTest extends \PHPUnit_Framework_TestCase
{
    public function testCanInstantiate()
    {
        new Document();
        $this->assertTrue(true);
    }

    public function testCanInstantiateWithData()
    {
        $obj = new Document([
            'hello' => 'world'
        ]);
        $this->assertEquals('world', $obj->hello);
    }

    public function testCanAddBulkMetaProperties()
    {
        $obj = new Document();
        $obj->setMetaProperties([
            'zowie' => 'bang',
            'glut' => 'rown'
        ]);

        $this->assertEquals('rown', $obj->{'@meta'}->glut);
    }

    public function testCanAddMetaProperty()
    {
        $obj = new Document();
        $obj->setMetaProperty('zowie', 'bang');

        $this->assertEquals('bang', $obj->{'@meta'}->zowie);
    }

    public function testCanAddMetaControl()
    {
        $obj = new Document();
        $obj->setMetaControl('self', '/path');
        $this->assertEquals('/path', $obj->{'@meta'}->{'@controls'}->self->href);
    }

    public function testCanAddNamespace()
    {
        $obj = new Document();
        $obj->addNamespace('go', 'http://go.example.com');
        $this->assertEquals('http://go.example.com', $obj->{'@namespaces'}->go->name);
    }

    public function testCanAddNamespaces()
    {
        $obj = new Document();
        $obj->setNamespaces([
            'go' => 'http://go.example.com',
            'zz' => new MasonNamespace('http://zz.example.com')
        ]);
        $this->assertEquals('http://zz.example.com', $obj->{'@namespaces'}->zz->name);
    }

    public function testCanSetError()
    {
        $obj = new Document();
        $obj->setError('It broke dude', ['@id' => 'bad-dog']);
        $this->assertEquals('bad-dog', $obj->{'@error'}->{'@id'});
    }

    public function testCanMinimize()
    {
        $obj = new Document();
        $obj->setControl('self', '/path', ['title' => 'My Title']);
        $obj->minimize();

        $this->assertObjectNotHasAttribute('title', $obj->{'@controls'}->self);
    }

    public function testMetaDisappearsIfNoPropertiesOnMinimization()
    {
        $obj = new Document();
        $obj->setMetaProperty('foo', 'bar');
        $obj->minimize();

        $this->assertObjectNotHasAttribute('@meta', $obj);
    }

    public function testCanInstantiateWithVanillaObjectHierarchy()
    {
        $obj1 = (new Document())
            ->setControl('self', '/foo/bar/baz', ['title' => 'The baffle ball'])
            ->addNamespace('pcom', 'http://example.com/relations/#')
            ->setControl('pcom:razz', '/razz', [
                'files' => [
                    ['name' => 'avatar', 'accept' => ['image/*'],
                        'title' => 'Avatar', 'description' => 'Photo of granny']
                ],
                'title' => 'This is the non-minimized title',
            ])
            ->setMetaControl('down', '/handprints/145')
            ->setMetaProperties([
                '@title' => 'Yipee!',
            ])
            ->setMetaProperty('foo', 'bar')
            ->setProperty('items', [
                new Child(['foo' => 'baz', 'phone' => 'shibboleth'])
            ])
            ->setError('This hoodie doesn\'t fit!', [
                '@code' => 'idk',
                '@httpStatusCode' => 422,
                '@messages' => [
                    'Hood too small',
                    'Zipper stuck',
                    'Pocket too shallow'
                ],
                '@details' => 'This article of clothing is either too small or too large for the nerd who is '
                    . 'wearing it',
                '@id' => 849384832,
                '@time' => '2015-07-17T18:37:59.52Z'
            ])
            ->sort(['@meta', '@controls', '@namespaces', '{data}']);

        $obj1Json = json_encode($obj1);

        $obj2 = new Document(json_decode($obj1Json));
        $obj2Json = json_encode($obj2);

        $this->assertEquals($obj1Json, $obj2Json);
    }
}

<?php
namespace PhoneCom\Mason\Schema;

class DocumentSchema extends JsonSchema
{
    /*
    private static $sharedSchemaRefUrlPrefix = '';
    private static $masonSchemaRefUrlPrefix = '';

    public static function setSharedSchemaRefUrlPrefixes(array $prefixes)
    {
        self::$sharedSchemaRefUrlPrefix = (string)@$prefixes['shared'];
        self::$masonSchemaRefUrlPrefix = (string)@$prefixes['mason'];
    }

    protected static function getMasonSchemaRefUrl($name)
    {
        return self::$masonSchemaRefUrlPrefix . "#definitions/$name";
    }

    protected static function getSharedSchemaRefUrl($name)
    {
        return self::$sharedSchemaRefUrlPrefix . "#definitions/$name";
    }
    */

    public function __construct($properties = [])
    {
        parent::__construct($properties);

        $masonSchemaUrl = 'https://raw.githubusercontent.com/JornWildt/Mason/master'
            . '/Documentation/Schema/Mason-draft-2.json';

        $this
            ->setPropertyRef('@namespaces', "$masonSchemaUrl#/properties/@namespaces")
            ->setProperty('@meta', JsonSchema::make([
                'allOf' => [
                    ['$ref' => "$masonSchemaUrl#/properties/@meta"]
                ]
            ]))
            ->setProperty('@error', JsonSchema::make([
                'allOf' => [
                    ['$ref' => "$masonSchemaUrl#/properties/@error"]
                ]
            ]))
            ->setPropertyRef('@controls', "$masonSchemaUrl#/properties/@controls")
            ->setAdditionalProperties(false);
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function setMetaProperty($name, $type, $title = '', $extraParams = [])
    {
        if (!isset($this->properties->{'@meta'})) {
            $this->setOptionalProperty('@meta', 'object', ['title' => 'Mason meta properties']);
        }

        if (!isset($this->properties->{'@meta'}->properties)) {
            $this->properties->{'@meta'}->properties = new \stdClass;
        }

        $property = JsonSchema::make(['type' => $type]);

        if ($title) {
            $property->setTitle($title);
        }

        if ($extraParams) {
            $property->addParams($extraParams);
        }

        $this
            ->properties
                ->{'@meta'}
                    ->properties
                        ->{$name} = $property;

        return $this;
    }

    protected function setControlSchema($name, JsonSchema $control)
    {
        if (!isset($this->properties->{'@controls'})) {
            $this->setOptionalProperty('@controls', 'object', ['title' => 'Mason controls']);
        }

        if (!isset($this->properties->{'@controls'}->properties)) {
            $this->properties->{'@controls'}->properties = new \stdClass;
        }

        $this
            ->properties
                ->{'@controls'}
                    ->properties
                        ->{$name} = $control;

        return $this;
    }
}

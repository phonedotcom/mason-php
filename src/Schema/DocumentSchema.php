<?php
namespace PhoneCom\Mason\Schema;

class DocumentSchema extends JsonSchema
{
    public function __construct($properties = [])
    {
        parent::__construct($properties);

        // TODO: It's breaking MVC to require a controller as a dependency on a model. Find some other way
        // TODO: to get a $ref to the @meta, @namespaces, and @controls definitions.

        $this
            ->setProperty('@namespaces', 'object', ['title' => 'Mason namespaces'])
            ->setProperty('@meta', 'object', ['title' => 'Mason meta properties'])
            ->setProperty('@controls', 'object', ['title' => 'Mason controls']);
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

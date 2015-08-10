<?php
namespace PhoneCom\Mason\Schema;

class DocumentSchema extends JsonSchema
{
    public function __construct($request = null, $properties = [])
    {
        parent::__construct($request, $properties);

        $this
            ->setOptionalProperty('@namespaces', 'object', ['title' => 'Mason namespaces'])
            ->setOptionalProperty('@meta', 'object', ['title' => 'Mason meta properties'])
            ->setOptionalProperty('@controls', 'object', ['title' => 'Mason controls']);
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

        $property = SubSchema::make()
            ->setType($type);

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

    protected function setControlSchema($name, SubSchema $control)
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

<?php
namespace PhoneCom\Mason\Schema;

class SubSchema
{
    public static function make($request = null, $properties = [])
    {
        return new static($request, $properties);
    }

    public function __construct($request = null, $properties = [])
    {
        if ($properties) {
            foreach ($properties as $property => $value) {
                $this->{$property} = $value;
            }
        }
    }

    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function setAdditionalProperties($value)
    {
        $this->additionalProperties = $value;

        return $this;
    }

    public function setMaxProperties($number)
    {
        $this->maxProperties = $number;

        return $this;
    }

    public function setMinProperties($number)
    {
        $this->minProperties = $number;

        return $this;
    }

    public function addDefinition($name, $params = [])
    {
        if (!isset($this->definitions)) {
            $this->definitions = new \stdClass;
        }
        $this->definitions->{$name} = (object)$params;

        return $this;
    }

    public function setRequiredProperty($name, $type, $title = '', $extraProperties = [])
    {
        $this->setOptionalProperty($name, $type, $title, $extraProperties);

        if (!isset($this->required)) {
            $this->required = [];
        }
        if (!in_array($name, $this->required)) {
            $this->required[] = $name;
        }

        return $this;
    }

    public function setRequiredPropertyRef($name, $ref, $title = '', $extraParams = [])
    {
        $this->setOptionalPropertyRef($name, $ref, $title, $extraParams);

        if (!isset($this->required)) {
            $this->required = [];
        }
        $this->required[] = $name;

        return $this;
    }

    public function setOptionalPropertyRef($name, $ref, $title = '', $extraParams = [])
    {
        if (isset($this->type) && $this->type != 'object') {
            throw new \InvalidArgumentException(sprintf(
                'Schema type is "%s", but properties can only be added to schemas of type "object"',
                $this->type
            ));
        }

        $this->type = 'object';

        if ($ref instanceof self) {
            $property = $ref;

        } else {
            $property = SubSchema::make()
                ->addParams(['$ref' => $ref]);

            if ($title) {
                $property->setTitle($title);
            }

            if ($extraParams) {
                $property->addParams($extraParams);
            }
        }

        if (!isset($this->properties)) {
            $this->properties = new \stdClass;
        }
        $this->properties->{$name} = $property;

        return $this;
    }

    public function setArraySchema($type, array $list)
    {
        $this->{$type} = $list;

        return $this;
    }

    public function setPatternProperty($name, $type, $title = '', $extraParams = [])
    {
        if (isset($this->type) && $this->type != 'object') {
            throw new \InvalidArgumentException(sprintf(
                'Schema type is "%s", but properties can only be added to schemas of type "object"',
                $this->type
            ));
        }

        $this->type = 'object';

        if ($type instanceof self) {
            $property = $type;

        } else {
            $property = SubSchema::make()
                ->setType($type);

            if ($title) {
                $property->setTitle($title);
            }

            if ($extraParams) {
                $property->addParams($extraParams);
            }
        }

        if (!isset($this->patternProperties)) {
            $this->patternProperties = new \stdClass;
        }
        $this->patternProperties->{$name} = $property;

        return $this;
    }

    public function setOptionalProperty($name, $type, $title = '', $extraParams = [])
    {
        if (isset($this->type) && $this->type != 'object') {
            throw new \InvalidArgumentException(sprintf(
                'Schema type is "%s", but properties can only be added to schemas of type "object"',
                $this->type
            ));
        }

        $this->type = 'object';

        if ($type instanceof self) {
            $property = $type;

        } else {
            $property = SubSchema::make()
                ->setType($type);

            if ($title) {
                $property->setTitle($title);
            }

            if ($extraParams) {
                $property->addParams($extraParams);
            }
        }

        if (!isset($this->properties)) {
            $this->properties = new \stdClass;
        }
        $this->properties->{$name} = $property;

        return $this;
    }

    public function addParams(array $params = [])
    {
        foreach ($params as $name => $value) {
            $this->{$name} = $value;
        }

        return $this;
    }

    public static function sortSchemaProperties($object)
    {
        $rootProperties = get_object_vars($object);

        foreach ($rootProperties as $property => $value) {
            unset($object->{$property});

            if (is_object($value)) {
                self::sortSchemaProperties($value);

            } elseif (is_array($value)) {
                foreach ($value as $subvalue) {
                    if (is_object($subvalue)) {
                        self::sortSchemaProperties($subvalue);
                    }
                }
            }
        }

        $preferredTopOrder = [
            'id', '$schema', '$ref', 'title', 'description', 'type', 'properties', '@meta',
        ];

        foreach ($preferredTopOrder as $property) {
            if (isset($rootProperties[$property])) {
                $object->{$property} = $rootProperties[$property];
                unset($rootProperties[$property]);
            }
        }

        $preferredBottomOrder = ['@controls', '@namespaces'];

        foreach ($rootProperties as $property => $value) {
            if (!in_array($property, $preferredBottomOrder)) {
                $object->{$property} = $value;
            }
        }

        foreach ($preferredBottomOrder as $property) {
            if (isset($rootProperties[$property])) {
                $object->{$property} = $rootProperties[$property];
                unset($rootProperties[$property]);
            }
        }

    }
}

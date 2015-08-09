<?php
namespace PhoneCom\Mason\Schema;

class JsonSchema
{
    public static function make($params = [])
    {
        return new static($params);
    }

    public function __construct($params = [])
    {
        $this->setParams($params);
    }

    public function setParams($params)
    {
        if (!is_object($params) && !is_array($params)) {
            throw new \InvalidArgumentException(sprintf('Object or array expected, got %s', gettype($params)));
        }

        foreach ($params as $key => $value) {
            $setter = 'set' . ucfirst($key);
            if (method_exists($this, $setter)) {
                $this->$setter($value);
            } else {
                $this->{$key} = $value;
            }
        }

        return $this;
    }

    public function setId($value)
    {
        $this->id = $value;

        return $this;
    }

    public function setSchema($value)
    {
        $this->{'$schema'} = $value;

        return $this;
    }

    public function setRef($value)
    {
        $this->{'$ref'} = $value;

        return $this;
    }

    public function setTitle($value)
    {
        $this->title = $value;

        return $this;
    }
    
    public function setDescription($value)
    {
        $this->description = $value;

        return $this;
    }

    public function setType($value)
    {
        $this->type = $value;

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

    public function setAdditionalProperties($value)
    {
        $this->additionalProperties = $value;

        return $this;
    }

    public function setRequired(array $value)
    {
        $this->required = $value;

        return $this;
    }

    public function setDefinition($name, JsonSchema $defintion)
    {
        if (!isset($this->definitions)) {
            $this->definitions = [];
        }

        $this->definitions[$name] = $defintion;

        return $this;
    }

    public function addRequired($value)
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        if (!isset($this->required)) {
            $this->required = [];
        }

        $this->required = array_merge($this->required, $value);

        return $this;
    }

    public function setRequiredPropertyRef($name, $url)
    {
        return $this->setRequiredProperty($name, new self([
            '$ref' => $url
        ]));
    }

    public function setRequiredProperty($name, $type, $params = [])
    {
        $this->setProperty($name, $type, $params);
        $this->addRequired($name);

        return $this;
    }

    public function setOptionalProperty($name, $type, $params = [])
    {
        return $this->setProperty($name, $type, $params);
    }

    public function setOptionalPropertyRef($name, $url)
    {
        return $this->setProperty($name, new self([
            '$ref' => $url
        ]));
    }

    public function setProperty($name, $type, $params = [])
    {
        if (!isset($this->properties)) {
            $this->setType('object');
            $this->properties = new self;
        }

        if ($type instanceof self) {
            $schema = $type;

        } else {
            $schema = (new self)
                ->setType($type)
                ->setParams($params);
        }

        $this->properties->{$name} = $schema;

        return $this;
    }

    public function setPatternProperty($pattern, $type, $params = [])
    {
        if (!isset($this->patternProperties)) {
            $this->patternProperties = [];
        }

        if ($type instanceof self) {
            $schema = $type;

        } else {
            $schema = (new self)
                ->setType($type)
                ->setParams($params);
        }

        $this->patternProperties[$pattern] = $schema;

        return $this;
    }

    public function sort(array $order)
    {
        if (!in_array('{data}', $order)) {
            $order[] = '{data}';
        }

        $this->applySort($order);

        return $this;
    }

    private function applySort(array &$order)
    {
        $data = self::getPublicProperties($this);
        foreach ($data as $property => $value) {
            if ($value instanceof self) {
                $value->applySort($order);

            } elseif (is_array($value)) {
                $this->sortArray($value, $order);
            }
            unset($this->{$property});
        }

        $unorderedProperties = array_diff(array_keys($data), $order);

        foreach ($order as $property) {
            if ($property == '{data}') {
                foreach ($unorderedProperties as $unorderedProperty) {
                    $this->{$unorderedProperty} = $data[$unorderedProperty];
                }

            } elseif (isset($data[$property])) {
                $this->{$property} = $data[$property];
            }
        }

        return $this;
    }

    private function sortArray(array &$array, array &$order)
    {
        foreach ($array as $index => &$value) {
            if ($value instanceof self) {
                $value->applySort($order);

            } elseif (is_array($value)) {
                $this->sortArray($value, $order);
            }
        }
    }

    private static function getPublicProperties(self $object)
    {
        return get_object_vars($object);
    }
}

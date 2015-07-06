<?php
namespace PhoneCom\Mason\Builder\Components;

class MasonNamespace extends Base
{
    /**
     * @param string $name URI or URI template
     * @param array $properties
     */
    public function __construct($name)
    {
        $this->setName($name);
    }

    /**
     * @param string $name URI for the namespace
     * @return $this
     */
    public function setName($name)
    {
        $this->name = (string)$name;

        return $this;
    }
}

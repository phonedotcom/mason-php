<?php
namespace PhoneCom\Mason\Builder\Components;

class Hash extends Base
{
    /**
     * @param string $name Property name
     * @param mixed $value Property value
     * @return $this
     */
    public function setProperty($name, $value)
    {
        // The Base class does not allow setting any properties unless they have a setter method.
        // But Hash needs to allow custom ones.

        $setter = 'set' . ucfirst($name);
        if (method_exists($this, $setter)) {
            $this->{$setter}($value);
        } else {
            $this->{$name} = $value;
        }

        return $this;
    }

    public function __set($name, $value)
    {
        // The Base class does not allow setting any properties unless they have a setter method.
        // But Hash needs to allow custom ones.

        $this->{$name} = $value;
    }
}

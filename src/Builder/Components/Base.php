<?php
namespace PhoneCom\Mason\Builder\Components;

abstract class Base
{
    /**
     * @param array $properties List of properties to set
     */
    public function __construct($properties = [])
    {
        $this->setProperties($properties);
    }

    /**
     * @param array $properties List of properties to set
     * @return $this
     */
    public function setProperties($properties)
    {
        foreach ($properties as $name => $value) {
            $this->setProperty($name, $value);
        }

        return $this;
    }

    /**
     * @param string $name Property name
     * @param mixed $value Value of the property
     * @return $this
     */
    public function setProperty($name, $value)
    {
        $setter = 'set' . ucfirst($name);
        $this->$setter($value);

        return $this;
    }

    public function __set($name, $value)
    {
        $setter = 'set' . ucfirst($name);
        if (!method_exists($this, $setter)) {
            throw new \InvalidArgumentException(sprintf('Invalid property "%s"', $name));
        }
        $this->{$name} = $value;
    }

    /**
     * Recursively remove all properties that don't belong in the minimal representation of this object
     * @return $this
     */
    public function minimize()
    {
        foreach (get_object_vars($this) as $property => $value) {

            if ($value instanceof self) {
                $value->minimize();

            } elseif (is_array($value)) {
                $this->minimizeArray($value);
            }
        }

        return $this;
    }

    private function minimizeArray(array &$array)
    {
        foreach ($array as $property => &$value) {
            if (is_array($value)) {
                $this->minimizeArray($value);

            } elseif ($value instanceof self) {
                $value->minimize();
            }
        }
    }

    /**
     * This method allows an object's properties to be sorted according to an arbitrary sequence of properties. This
     * is useful for standardizing the sequence of properties across several documents and for keeping the more
     * important properties higher up.
     *
     * @param array $defaultOrder Preferred order of property names. Can include any Mason or custom property. Ordering will
     *                     be applied at all levels within the document. Properties that are not found at a given
     *                     level are gracefully ignored. Must include an element named "{data}". This is where all
     *                     unspecified properties will be placed. All such properties will maintain the same order as
     *                     they had before sorting.
     * @return $this
     */
    public function sort(array $defaultOrder, array $controlsOrder = null, array $metaOrder = null, array $errorOrder = null)
    {
        if (!in_array('{data}', $defaultOrder)) {
            throw new \InvalidArgumentException('Placeholder "{data}" not listed in $defaultOrder');

        } elseif ($controlsOrder && !in_array('{data}', $controlsOrder)) {
            throw new \InvalidArgumentException('Placeholder "{data}" not listed in $controlsOrder');

        } elseif ($metaOrder && !in_array('{data}', $metaOrder)) {
            throw new \InvalidArgumentException('Placeholder "{data}" not listed in $metaOrder');

        } elseif ($errorOrder && !in_array('{data}', $errorOrder)) {
            throw new \InvalidArgumentException('Placeholder "{data}" not listed in $errorOrder');
        }

        $controlsOrder || $controlsOrder = $defaultOrder;
        $metaOrder || $metaOrder = $defaultOrder;
        $errorOrder || $errorOrder = $defaultOrder;

        $this->applySort('', $defaultOrder, $controlsOrder, $metaOrder, $errorOrder);

        return $this;
    }

    private function applySort($name, array &$defaultOrder, array &$controlsOrder, array &$metaOrder, array &$errorOrder)
    {
        $data = self::getPublicProperties($this);
        foreach ($data as $property => $value) {
            if ($value instanceof self) {
                $value->applySort($property, $defaultOrder, $controlsOrder, $metaOrder, $errorOrder);

            } elseif (is_array($value)) {
                $this->sortArray($value, $defaultOrder, $controlsOrder, $metaOrder, $errorOrder);
            }
            unset($this->{$property});
        }

        // In PHP, when comparing an integer on the left to a string on the right, the string gets cast to an integer
        // prior to comparison. This has the counter-intuitive effect with typical switch() statements that use strings
        // for all the cases, and you run integer 0 through it, the first 'case' comparison evaluates to true. Doh!
        // To prevent this, we need to make sure $name is a string when entering the switch below.
        //
        // For more discussion, see:
        // http://stackoverflow.com/questions/2611932/php-5-2-12-interesting-switch-statement-bug-with-integers-and-strings

        switch ((string)$name) {
            case '@controls':
                $order = &$controlsOrder;
                break;

            case '@meta':
                $order = &$metaOrder;
                break;

            case '@error':
                $order = &$errorOrder;
                break;

            default:
                $order = &$defaultOrder;
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

    private function sortArray(array &$array, array &$defaultOrder, array &$controlsOrder, array &$metaOrder, array &$errorOrder)
    {
        foreach ($array as $index => &$value) {
            if ($value instanceof self) {
                $value->applySort('', $defaultOrder, $controlsOrder, $metaOrder, $errorOrder);

            } elseif (is_array($value)) {
                $this->sortArray($value, $defaultOrder, $controlsOrder, $metaOrder, $errorOrder);
            }
        }
    }

    private static function getPublicProperties(self $object)
    {
        return get_object_vars($object);
    }
}

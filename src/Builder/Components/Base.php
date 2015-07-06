<?php
namespace PhoneCom\Mason\Builder\Components;

abstract class Base
{
    /**
     * @param array $properties List of properties to set
     */
    public function __construct(array $properties = [])
    {
        $this->setProperties($properties);
    }

    /**
     * @param array $properties List of properties to set
     * @return $this
     */
    public function setProperties(array $properties)
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
     * @param array $order Preferred order of property names. Can include any Mason or custom property. Ordering will
     *                     be applied at all levels within the document. Properties that are not found at a given
     *                     level are gracefully ignored. Must include an element named "{data}". This is where all
     *                     unspecified properties will be placed. All such properties will maintain the same order as
     *                     they had before sorting.
     * @return $this
     */
    public function sort(array $order)
    {
        if (!in_array('{data}', $order)) {
            throw new \InvalidArgumentException('Placeholder "{data}" not listed');
        }

        $data = self::getPublicProperties($this);
        foreach ($data as $property => $value) {
            if ($value instanceof self) {
                $value->sort($order);

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
        foreach ($array as $property => &$value) {
            if ($value instanceof self) {
                $value->sort($order);

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

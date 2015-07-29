<?php
namespace PhoneCom\Mason\Builder;

use PhoneCom\Mason\Builder\Components\Control;
use PhoneCom\Mason\Builder\Components\Controls;
use PhoneCom\Mason\Builder\Components\Hash;
use PhoneCom\Mason\Builder\Components\Namespaces;
use PhoneCom\Mason\Builder\Components\Meta;
use PhoneCom\Mason\Builder\Components\Error;

class Child extends Hash
{
    /**
     * @param array $properties List of properties to set
     * @return $this
     */
    public function setProperties($properties)
    {
        foreach ($properties as $name => $value) {
            if ($name == '@controls') {
                $controls = $value;
                if (!$controls instanceof Controls) {
                    $controls = new Controls($value);
                }
                $this->setProperty($name, $controls);

            } elseif ($name == '@meta') {
                $meta = ($value instanceof Meta ? $value : new Meta($value));
                $this->setProperty($name, $meta);

            } elseif ($name == '@namespaces') {
                $namespaces = ($value instanceof Namespaces ? $value : new Namespaces($value));
                $this->setProperty($name, $namespaces);

            } elseif ($name == '@error') {
                $error = $value;
                if (!$value instanceof Error) {
                    $properties = (array)$error;
                    $message = $properties['@message'];
                    unset($properties['@message']);
                    $error = new Error($message, $properties);
                }
                $this->setProperty($name, $error);

            } elseif (is_array($value)) {
                if (array_is_sequential($value)) {
                    $value = $this->prepareSequentialArray($value);
                    $this->setProperty($name, $value);

                } else {
                    $this->setProperty($name, new self($value));
                }

            } elseif (is_object($value)) {
                $this->setProperty($name, new self($value));

            } else {
                $this->setProperty($name, $value);
            }
        }

        return $this;
    }

    private function prepareSequentialArray(&$value)
    {
        foreach ($value as $index => $subvalue) {
            if (is_object($subvalue) && !$subvalue instanceof Base) {
                $value[$index] = new self($subvalue);

            } elseif (is_array($subvalue)) {
                if (array_is_sequential($subvalue)) {
                    $value[$index] = $this->prepareSequentialArray($subvalue);
                }
            }
        }

        return $value;
    }

    /**
     * @param string $name Name of property to set
     * @param mixed $value Value of the property
     * @return $this
     */
    public function setProperty($name, $value)
    {
        if (substr($name, 0, 1) == '@') {
            $setter = 'set' . ucfirst(substr($name, 1));
            $this->{$setter}($value);

        } else {
            $this->{$name} = $value;
        }

        return $this;
    }

    /**
     * @param array $controls List of controls to add. Array items can be instances of Control, or array with
     *                        'href' (required) and optionally, 'properties'
     * @return $this
     */
    public function setControls($controls)
    {
        unset($this->{'@controls'});

        foreach ($controls as $relation => $control) {
            if ($control instanceof Control) {
                $href = $control;
                $properties = [];

            } elseif (is_array($control) || is_object($control)) {
                $control = (array)$control;
                $href = $control['href'];
                $properties = (@$control['properties'] ?: []);

            } else {
                throw new \InvalidArgumentException(sprintf('Invalid control "%s"', $relation));
            }

            $this->setControl($relation, $href, $properties);
        }

        return $this;
    }

    /**
     * @param string $relation Link relation describing the control
     * @param string|Control $href URL or a Control instance
     * @param array $properties If $href is a URL, additional control properties to set
     * @return $this
     */
    public function setControl($relation, $href, $properties = [])
    {
        $this->prepareControlsNode();
        $this->{'@controls'}->setControl($relation, $href, $properties);

        return $this;
    }

    private function prepareControlsNode()
    {
        if (!isset($this->{'@controls'})) {
            $this->{'@controls'} = new Controls;
        }
    }
}

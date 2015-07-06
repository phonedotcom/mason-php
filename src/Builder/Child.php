<?php
namespace PhoneCom\Mason\Builder;

use PhoneCom\Mason\Builder\Components\Control;
use PhoneCom\Mason\Builder\Components\Controls;
use PhoneCom\Mason\Builder\Components\Hash;

class Child extends Hash
{
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
    public function setControls(array $controls)
    {
        unset($this->{'@controls'});

        foreach ($controls as $relation => $control) {
            if (is_array($control)) {
                $href = $control['href'];
                $properties = (@$control['properties'] ?: []);

            } elseif ($control instanceof Control) {
                $href = $control;
                $properties = [];

            } else {
                throw new \InvalidArgumentException(sprintf('Invalid control "%s"', $relation));
            }

            $this->addControl($relation, $href, $properties);
        }

        return $this;
    }

    /**
     * @param string $relation Link relation describing the control
     * @param string|Control $href URL or a Control instance
     * @param array $properties If $href is a URL, additional control properties to set
     * @return $this
     */
    public function addControl($relation, $href, $properties = [])
    {
        $this->prepareControlsNode();
        $this->{'@controls'}->addControl($relation, $href, $properties);

        return $this;
    }

    private function prepareControlsNode()
    {
        if (!isset($this->{'@controls'})) {
            $this->{'@controls'} = new Controls;
        }
    }
}

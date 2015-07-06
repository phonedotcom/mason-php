<?php
namespace PhoneCom\Mason\Builder\Components;

class Controls extends Hash
{
    /**
     * @param string $relation Link relation
     * @param string|Control $href URL or a Control instance
     * @param array $properties If $href is a string, list of additional properties to set
     * @return $this
     */
    public function addControl($relation, $href, array $properties = [])
    {
        if ($href instanceof Control) {
            $control = $href;
        } else {
            $control = new Control($href, $properties);
        }

        $this->{$relation} = $control;

        return $this;
    }

    /**
     * @param string $relation Name of relation to remove
     * @return $this
     */
    public function remove($relation)
    {
        unset($this->{$relation});

        return $this;
    }

}

<?php
namespace PhoneCom\Mason\Builder\Components;

class Controls extends Hash
{
    public function setProperties($properties = [])
    {
        foreach ($properties as $relation => $control) {
            if (!$control instanceof Control) {
                $properties = (array)$control;
                if (!isset($properties['href'])) {
                    throw new \InvalidArgumentException('Control has no href');
                }
                $href = $properties['href'];
                unset($properties['href']);

                $control = new Control($href, $properties);
            }
            $this->setControl($relation, $control);
        }

        return $this;
    }

    /**
     * @param string $relation Link relation
     * @param string|Control $href URL or a Control instance
     * @param array $properties If $href is a string, list of additional properties to set
     * @return $this
     */
    public function setControl($relation, $href, array $properties = [])
    {
        $control = ($href instanceof Control ? $href : new Control($href, $properties));
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

<?php
namespace Phonedotcom\Mason\Builder\Components;

use Phonedotcom\Mason\Builder\MasonControllableInterface;

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
     * @param string $relation Link relation describing the control, or a name of a class
     *     that implements MasonControllableInterface
     * @param string|Control $href URL, or a Control instance, or a name of a class that implements
     *     MasonControllableInterface
     * @param array $properties If $href is a string, list of additional properties to set
     * @return $this
     */
    public function setControl($relation, $href = null, array $properties = [])
    {
        if (class_exists($relation)) {
            $className = $relation;
            if (!(new $className) instanceof MasonControllableInterface) {
                throw new \Exception(sprintf('Class must implement MasonControllableInterface: %s', $className));
            }

            $relation = $className::getRelation();
            $properties = (is_null($href) ? [] : $href);
            $control = $className::getMasonControl($properties);

        } elseif ($href instanceof Control) {
            $control = $href;

        } elseif (is_string($href) && class_exists($href)) {
            $className = $href;
            if (!(new $className) instanceof MasonControllableInterface) {
                throw new \Exception(sprintf('Class must implement MasonControllableInterface: %s', $className));
            }
            $control = $className::getMasonControl($properties);

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

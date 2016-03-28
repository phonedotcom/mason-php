<?php
namespace Phonedotcom\Mason\Builder\Components;

use Phonedotcom\Mason\Builder\Child;
use Phonedotcom\Mason\Builder\MasonControllableInterface;

class Meta extends Child
{
    private $protected = [];
    private $protectedControls = [];

    /**
     * @param string $name Name of property to set
     * @param mixed $value Value of the property
     * @param bool $protected Whether to keep this property when minimized
     * @return $this
     */
    public function setProperty($name, $value, $protected = false)
    {
        parent::setProperty($name, $value);
        if ($protected) {
            $this->protected[] = $name;
        }

        return $this;
    }

    /**
     * @param string $title Descriptive title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->{'@title'} = (string)$title;

        return $this;
    }

    /**
     * @param string $description Descriptive text
     * @return $this
     */
    public function setDescription($description)
    {
        $this->{'@description'} = (string)$description;

        return $this;
    }

    /**
     * @param string $relation Link relation describing the control, or a name of a class
     *     that implements MasonControllableInterface
     * @param string|Control $href URL, or a Control instance, or a name of a class that implements
     *     MasonControllableInterface
     * @param array $properties If $href is a URL, additional control properties to set
     * @param bool $protected Whether to keep this control when minimized
     * @return $this
     */
    public function setControl($relation, $href = null, $properties = [], $protected = false)
    {
        parent::setControl($relation, $href, $properties);
        if ($protected) {
            $this->protected[] = '@controls';

            if (class_exists($relation)) {
                $className = $relation;
                if (!(new $className) instanceof MasonControllableInterface) {
                    throw new \Exception(sprintf('Class must implement MasonControllableInterface: %s', $className));
                }
                $relation = $className::getRelation();
            }

            $this->protectedControls[] = $relation;
        }

        return $this;
    }

    /**
     * Recursively remove all properties that don't belong in the minimal representation of this object
     * @return $this
     */
    public function minimize()
    {
        $this->removeUnprotectedControls();
        $this->removeUnprotectedProperties();
        parent::minimize();

        return $this;
    }

    private function removeUnprotectedControls()
    {
        if (isset($this->{'@controls'})) {
            $relationsToRemove = array_diff(
                array_keys(get_object_vars($this->{'@controls'})),
                $this->protectedControls
            );
            foreach ($relationsToRemove as $relation) {
                $this->{'@controls'}->remove($relation);
            }
            if (!get_object_vars($this->{'@controls'})) {
                unset($this->{'@controls'});
            }
        }
    }

    private function removeUnprotectedProperties()
    {
        $propertiesToRemove = array_diff(
            array_keys(get_object_vars($this)),
            $this->protected,
            ['protected', 'protectedControls']
        );

        foreach ($propertiesToRemove as $property) {
            unset($this->{$property});
        }
    }
}

<?php
namespace Phonedotcom\Mason\Builder;

use Phonedotcom\Mason\Builder\Components\Error;
use Phonedotcom\Mason\Builder\Components\MasonNamespace;
use Phonedotcom\Mason\Builder\Components\Meta;
use Phonedotcom\Mason\Builder\Components\Namespaces;

class Document extends Child
{
    /**
     * @param object|array|Meta $meta Meta properties
     * @return $this
     */
    public function setMeta($meta)
    {
        if (!$meta instanceof Meta) {
            $meta = new Meta($meta);
        }
        $this->{'@meta'} = $meta;

        return $this;
    }

    /**
     * @param array $properties List of meta properties to add
     * @param bool $protected Whether to keep these properties when object is minimized
     * @return $this
     */
    public function setMetaProperties(array $properties, $protected = false)
    {
        foreach ($properties as $property => $value) {
            $this->setMetaProperty($property, $value, $protected);
        }

        return $this;
    }

    /**
     * @param string $name Name of meta property
     * @param mixed $value Value of the property
     * @param bool $protected Whether to keep this property when object is minimized
     * @return $this
     */
    public function setMetaProperty($name, $value, $protected = false)
    {
        $this->prepareMetaNode();
        $this->{'@meta'}->setProperty($name, $value, $protected);

        return $this;
    }

    /**
     * @param string $relation Name of link relation
     * @param Control|string $href Control instance, URI, or URI template
     * @param array $properties Additional control properties if $href is a string
     * @param bool $protected Whether to keep this control when minimized
     * @return $this
     */
    public function setMetaControl($relation, $href, array $properties = [], $protected = false)
    {
        $this->prepareMetaNode();
        $this->{'@meta'}->setControl($relation, $href, $properties, $protected);

        return $this;
    }

    private function prepareMetaNode()
    {
        if (!isset($this->{'@meta'})) {
            $this->{'@meta'} = new Meta();
        }
    }

    /**
     * @param array $namespaces List of namespaces to add. Array items may be URLs or MasonNamespace instances
     * @return $this
     */
    public function setNamespaces($namespaces)
    {
        unset($this->{'@namespaces'});

        foreach ($namespaces as $name => $href) {
            $this->addNamespace($name, $href);
        }

        return $this;
    }

    /**
     * @param string $name Curie namespace name
     * @param string|MasonNamespace $href URL pointing to documentation for this namespace, or MasonNamespace instance
     * @return $this
     */
    public function addNamespace($name, $href)
    {
        $this->prepareNamespacesNode();
        $this->{'@namespaces'}->addNamespace($name, $href);

        return $this;
    }

    private function prepareNamespacesNode()
    {
        if (!isset($this->{'@namespaces'})) {
            $this->{'@namespaces'} = new Namespaces;
        }
    }

    /**
     * @param string $message Primary error message
     * @param array $properties Additional error properties
     * @param int|float|null $now Timestamp for the error, in UNIX or microtime(true) format
     * @return $this
     */
    public function setError($message, $properties = [], $now = null)
    {
        if (is_object($message) || is_array($message)) {
            $properties = (array)$message;
            $message = $properties['@message'];
            $now = @$properties['@time'];
            unset($properties['@message'], $properties['@time']);
        }
        $this->{'@error'} = new Error($message, $properties, $now);

        return $this;
    }

    /**
     * Remove all properties that are intended to be human-readable only
     * @return $this
     */
    public function minimize()
    {
        parent::minimize();

        if (isset($this->{'@meta'}) && !get_object_vars($this->{'@meta'})) {
            unset($this->{'@meta'});
        }

        return $this;
    }
}

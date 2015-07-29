<?php
namespace PhoneCom\Mason\Builder\Components;

class Namespaces extends Hash
{
    /**
     * @param string $name Namespace name, or MasonNamespace instance
     * @param string|MasonNamespace $href URL pointing to documentation on the namespace, or MasonNamespace
     * @return $this
     */
    public function addNamespace($name, $href)
    {
        if ($href instanceof MasonNamespace) {
            $namespace = $href;

        } elseif (is_object($href)) {
            $href = get_object_vars($href);
            $namespace = new MasonNamespace($href['name']);

        } else {
            $namespace = new MasonNamespace($href);
        }
        $this->{$name} = $namespace;

        return $this;
    }
}

<?php
namespace PhoneCom\Mason\Schema;

class JsonSchema extends SubSchema
{
    const DESCRIPTOR_URL = 'http://json-schema.org/draft-04/schema#';

    /**
     * @deprecated Schema does not need the $request object any more
     */
    protected $request;

    public function __construct($request = null, $properties = [])
    {
        parent::__construct($request, $properties);

        $this->request = $request;

        $this->{'$schema'} = self::DESCRIPTOR_URL;
    }

    public static function getName()
    {
        $className = get_called_class();
        $name = substr($className, strrpos($className, '\\') + 1);
        return snake_case($name, '-');
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}

<?php
namespace PhoneCom\Mason\Builder\Components;

class Control extends Base
{
    const ENCODING_NONE = 'none';
    const ENCODING_JSON = 'json';
    const ENCODING_JSON_FILES = 'json+files';
    const ENCODING_RAW = 'raw';

    public static $httpMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

    public static $encodings = [
        self::ENCODING_NONE, self::ENCODING_JSON, self::ENCODING_JSON_FILES, self::ENCODING_RAW
    ];

    /**
     * @param string $href URI or URI template
     * @param array $properties
     */
    public function __construct($href, array $properties = [])
    {
        $this->setHref($href);
        parent::__construct($properties);
    }

    /**
     * @param string $href Hypermedia reference - a URI or URI template
     * @return $this
     */
    public function setHref($href)
    {
        $this->href = (string)$href;

        return $this;
    }

    /**
     * @param bool $isHrefTemplate Whether "href" is a URI template or concrete URI (default = false)
     * @return $this
     */
    public function setIsHrefTemplate($isHrefTemplate)
    {
        $this->isHrefTemplate = (bool)$isHrefTemplate;

        return $this;
    }

    /**
     * @param string $title Title of the control
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = (string)$title;

        return $this;
    }

    /**
     * @param $description Description of the control
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = (string)$description;

        return $this;
    }

    /**
     * @param string $schemaUrl URL to referenced schema definition of request body and href template parameters
     * @return $this
     */
    public function setSchemaUrl($schemaUrl)
    {
        $this->schemaUrl = (string)$schemaUrl;

        return $this;
    }

    /**
     * @param object $schema Embedded schema definition of request body and href template parameters.
     * @return $this
     */
    public function setSchema($schema)
    {
        if (!is_object($schema)) {
            throw new \InvalidArgumentException(sprintf('Schema must be an object, %s given', gettype($schema)));
        }

        $this->schema = $schema;

        return $this;
    }

    /**
     * @param object $template Request template data
     * @return $this
     */
    public function setTemplate($template)
    {
        if (!is_object($template)) {
            throw new \InvalidArgumentException(sprintf('Template must be an object, %s given', gettype($template)));
        }

        $this->template = $template;

        return $this;
    }

    /**
     * @param array $accept List of accepted media types
     * @return $this
     */
    public function setAccept(array $accept)
    {
        $this->accept = [];
        foreach ($accept as $mediaType) {
            $this->addAccept($mediaType);
        }

        return $this;
    }

    /**
     * @param string $mediaType Accepted media type, e.g. 'image/jpeg'
     * @return $this
     */
    public function addAccept($mediaType)
    {
        if (!is_string($mediaType)) {
            throw new \InvalidArgumentException(sprintf('Accept must be a string, %s given', gettype($mediaType)));
        }

        $this->accept[] = $mediaType;

        return $this;
    }

    /**
     * @param array $output List of output media types
     * @return $this
     */
    public function setOutput(array $output)
    {
        $this->output = [];
        foreach ($output as $mediaType) {
            $this->addOutput($mediaType);
        }

        return $this;
    }

    /**
     * @param string $mediaType Output media type, e.g. 'application/json'
     * @return $this
     */
    public function addOutput($mediaType)
    {
        if (!is_string($mediaType)) {
            throw new \InvalidArgumentException(sprintf('Output must be a string, %s given', gettype($mediaType)));
        }

        $this->output[] = $mediaType;

        return $this;
    }

    /**
     * @param string $method HTTP method to be used in the request
     * @return $this
     */
    public function setMethod($method)
    {
        $method = strtoupper($method);
        if (!in_array($method, self::$httpMethods)) {
            throw new \InvalidArgumentException(sprintf('Invalid HTTP method "%s"', $method));
        }

        $this->method = $method;

        return $this;
    }

    /**
     * @param string $encoding Required encoding of the request body. Valid values are:
     *                              'none', 'json', 'json+files', and 'raw'
     * @return $this
     */
    public function setEncoding($encoding)
    {
        if (!in_array($encoding, self::$encodings)) {
            throw new \InvalidArgumentException(sprintf('Invalid encoding "%s"', $encoding));
        }
        $this->encoding = (string)$encoding;

        return $this;
    }

    /**
     * @param array $files List of details about accepted input files e.g. in a multipart/form-data request. Items
     *                      in the array must be File objects or simple arrays with the needed properties in the
     *                      order specified in the constructor for the File object
     * @return $this
     */
    public function setFiles(array $files)
    {
        foreach ($files as $file) {
            $this->addFile($file);
        }

        return $this;
    }

    /**
     * @param array|File $file Details about an accepted input file
     */
    public function addFile($file)
    {
        if (is_array($file)) {
            $name = $file['name'];
            unset($file['name']);
            $properties = $file;

            $file = (new File($name))
                ->setProperties($properties);

        } elseif (!$file instanceof File) {
            throw new \InvalidArgumentException(sprintf('File must be instance of %s or an array of acceptable file properties', File::class));
        }

        $this->files[] = $file;

        return $this;
    }

    /**
     * @param array $alternates List of alternate controls to the primary one. Items
     *                      in the array must be Control objects or simple arrays with the needed properties in the
     *                      order specified in the constructor for the Control object
     * @return $this
     */
    public function setAlt(array $alternates)
    {
        $this->alt = [];
        foreach ($alternates as $control) {
            $this->addAlt($control);
        }

        return $this;
    }

    /**
     * @param array|Control $control Alternate control representing the primary
     */
    public function addAlt($control)
    {
        if (is_array($control)) {
            $href = $control['href'];
            unset($control['href']);
            $properties = $control;

            $control = (new Control($href, $properties));

        } elseif (!$control instanceof Control) {
            throw new \InvalidArgumentException(sprintf('Control must be instance of %s or an array of acceptable control properties', Control::class));
        }

        $this->alt[] = $control;

        return $this;
    }

    /**
     * Recursively remove all properties that don't belong in the minimal representation of this object
     * @return $this
     */
    public function minimize()
    {
        unset($this->title, $this->description);
        parent::minimize();

        return $this;
    }
}

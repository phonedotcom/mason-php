<?php
namespace Phonedotcom\Mason\Builder\Components;

class File extends Base
{
    /**
     * @param string $name Name of the multipart element where the file data is embedded
     * @param array $accept List of accepted media types
     * @param string $title Title of the file
     * @param string $description Description of the file
     */
    public function __construct($name, $accept = [], $title = '', $description = '')
    {
        $this->setName($name);
        if ($accept) {
            $this->setAccept($accept);
        }
        if ($title) {
            $this->setTitle($title);
        }
        if ($description) {
            $this->setDescription($description);
        }
    }

    /**
     * @param string $name Name of the multipart element where the file data is embedded
     * @return $this
     */
    public function setName($name)
    {
        $this->name = (string)$name;

        return $this;
    }

    /**
     * @param string $title Title of the file
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = (string)$title;

        return $this;
    }

    /**
     * @param $description Description of the file
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = (string)$description;

        return $this;
    }

    /**
     * @param array $accept List of accepted media types
     * @return $this
     */
    public function setAccept(array $accept)
    {
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

<?php
namespace PhoneCom\Mason\Builder\Components;

use PhoneCom\Mason\Builder\Child;

class Error extends Child
{
    /**
     * @param array string $message Human readable error message directed at the end user
     * @param array $properties List of additional error properties to set
     * @param int|float|null $now Timestamp that the error should be marked with, in UNIX or PHP's microtime(true)
     */
    public function __construct($message, array $properties = [], $now = null)
    {
        $this->setMessage($message);
        parent::__construct($properties);

        if (!isset($properties['@time'])) {
            $now || $now = microtime(true);
            $this->setTime($now);
        }
    }

    /**
     * @param string $id Unique identifier for later reference to the situation that resulted in an error
     *                   condition (for instance when looking up a log entry)
     * @return $this
     */
    public function setId($id)
    {
        $this->{'@id'} = (string)$id;
        
        return $this;
    }

    /**
     * @param string $message Human readable error message directed at the end user
     * @return $this
     */
    public function setMessage($message)
    {
        $this->{'@message'} = (string)$message;

        return $this;
    }

    /**
     * @param string $code Code describing the error condition in general
     * @return $this
     */
    public function setCode($code)
    {
        $this->{'@code'} = (string)$code;

        return $this;
    }

    /**
     * @param string $details Extensive human readable message directed at the client developer
     * @return $this
     */
    public function setDetails($details)
    {
        $this->{'@details'} = (string)$details;

        return $this;
    }

    /**
     * @param int|float $timestamp Timestamp that the error should be marked with, in UNIX or PHP microtime(true) format
     * @return $this
     */
    public function setTime($timestamp)
    {
        if (is_int($timestamp) || is_float($timestamp)) {
            $fraction = $timestamp - floor($timestamp);
            $this->{'@time'} = gmdate('Y-m-d\TH:i:s', floor($timestamp))
                . ($fraction ? substr(round($fraction, 2), 1) : '') . 'Z';

        } else {
            $this->{'@time'} = $timestamp;
        }

        return $this;
    }

    /**
     * @param int $httpStatusCode HTTP status code from the latest response
     * @return $this
     */
    public function setHttpStatusCode($httpStatusCode)
    {
        $this->{'@httpStatusCode'} = (int)$httpStatusCode;

        return $this;
    }

    /**
     * @param array $messages List of additional human readable error messages directed at the end user
     * @return $this
     */
    public function setMessages(array $messages)
    {
        foreach ($messages as $message) {
            $this->addMessage($message);
        }

        return $this;
    }

    /**
     * @param string $message Human readable error messages directed at the end user
     * @return $this
     */
    public function addMessage($message)
    {
        $this->{'@messages'}[] = (string)$message;

        return $this;
    }
}

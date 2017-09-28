<?php

namespace Kutny\HttpClientBundle\Client;

class Response
{
    const HTTP_OK = 200;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;
    const HTTP_NOT_ACCEPTABLE = 406;
    const HTTP_SERVICE_UNAVAILABLE = 503;

    private $body;
    private $status;
    private $headers;

    public function __construct($body, $status, array $headers = [])
    {
        $this->body = $body;
        $this->status = (int) $status;
        $this->headers = $headers;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getBodySize()
    {
        return strlen($this->body);
    }

    public function hasHeader($name)
    {
        $normalizedName = strtolower($name);

        return array_key_exists($normalizedName, $this->headers);
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getHeader($name)
    {
        if (!$this->hasHeader($name)) {
            throw new \Exception('Response does NOT contain header "' . $name . '"');
        }

        $normalizedName = strtolower($name);

        return $this->headers[$normalizedName];
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function isError()
    {
        $restype = floor($this->status / 100);

        return ($restype == 4 || $restype == 5);
    }

    public function isSuccessful()
    {
        $restype = floor($this->status / 100);

        return ($restype == 2 || $restype == 1);
    }

    public function isRedirect()
    {
        $restype = floor($this->status / 100);

        return ($restype == 3);
    }
}

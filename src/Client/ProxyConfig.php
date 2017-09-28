<?php

namespace Kutny\HttpClientBundle\Client;

class ProxyConfig
{
    private $url;
    private $port;
    private $username;
    private $password;

    public function __construct($url, $port, $username = null, $password = null)
    {
        $this->url = $url;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getUsername()
    {
        return $this->username;
    }
}

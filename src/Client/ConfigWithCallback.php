<?php

namespace Kutny\HttpClientBundle\Client;

class ConfigWithCallback
{
    private $config;
    private $successCallback;
    private $errorCallback;

    public function __construct(Config $config, $successCallback, $errorCallback)
    {
        $this->config = $config;
        $this->successCallback = $successCallback;
        $this->errorCallback = $errorCallback;
    }

    public function getSuccessCallback()
    {
        return $this->successCallback;
    }

    public function getErrorCallback()
    {
        return $this->errorCallback;
    }

    public function getConfig()
    {
        return $this->config;
    }
}

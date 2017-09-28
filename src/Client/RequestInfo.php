<?php

namespace Kutny\HttpClientBundle\Client;

class RequestInfo
{
    private $finalUrl;
    private $loadTime;

    public function __construct($finalUrl, $loadTime)
    {
        $this->finalUrl = $finalUrl;
        $this->loadTime = $loadTime;
    }

    public function getFinalUrl()
    {
        return $this->finalUrl;
    }

    public function getLoadTime()
    {
        return $this->loadTime;
    }
}

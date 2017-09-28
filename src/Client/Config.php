<?php

namespace Kutny\HttpClientBundle\Client;

class Config
{
    const METHOD_POST = 'POST';
    const METHOD_GET = 'GET';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    const METHOD_OPTIONS = 'OPTIONS';

    private $url;
    private $userAgent;
    private $timeout;
    private $connectionTimeout;
    private $sslCertificateValidation;
    private $responseBodySizeLimit;
    private $cookiesStorageFile;
    private $maxRedirects;
    private $postData;
    private $method;
    private $proxyConfig;
    private $logFilePath;
    private $credentials;
    private $headers;

    public function __construct($url, $method = self::METHOD_GET)
    {
        $this->url = $url;
        $this->useCookies = false;
        $this->sslCertificateValidation = true;
        $this->method = $method;
        $this->timeout = 45;
        $this->connectionTimeout = 30;
        $this->headers = [];
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
    }

    public function getUserAgent()
    {
        return $this->userAgent;
    }

    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    public function getTimeout()
    {
        return $this->timeout;
    }

    public function setConnectionTimeout($connectionTimeout)
    {
        $this->connectionTimeout = $connectionTimeout;
    }

    public function getConnectionTimeout()
    {
        return $this->connectionTimeout;
    }

    public function setCookiesStorageFile($cookiesStorageFile)
    {
        $this->cookiesStorageFile = $cookiesStorageFile;
    }

    public function getCookiesStorageFile()
    {
        return $this->cookiesStorageFile;
    }

    public function setMaxRedirects($maxRedirects)
    {
        $this->maxRedirects = $maxRedirects;
    }

    public function getMaxRedirects()
    {
        return $this->maxRedirects;
    }

    public function setPostData(IPostData $postData, $method = self::METHOD_POST)
    {
        $this->postData = $postData;
        $this->method = $method;
    }

    /** @return IPostData */
    public function getPostData()
    {
        return $this->postData;
    }

    public function setMethod($method)
    {
        $this->method = strtoupper($method);
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setProxyConfig(ProxyConfig $proxyConfig = null)
    {
        $this->proxyConfig = $proxyConfig;
    }

    /** @return ProxyConfig */
    public function getProxyConfig()
    {
        return $this->proxyConfig;
    }

    public function disableSslCertificateValidation()
    {
        $this->sslCertificateValidation = false;
    }

    public function getSslCertificateValidation()
    {
        return $this->sslCertificateValidation;
    }

    public function setResponseBodySizeLimit($responseBodySizeLimit)
    {
        $this->responseBodySizeLimit = $responseBodySizeLimit;
    }

    public function getResponseBodySizeLimit()
    {
        return $this->responseBodySizeLimit;
    }

    public function setHeader($name, $value, $overwrite = false)
    {
        if (array_key_exists($name, $this->headers) && $overwrite === false) {
            throw new \Exception('Header "' . $name . '" already defined');
        }

        $this->headers[$name] = $value;
    }

    public function setHeaders(array $headers, $overwrite = false)
    {
        foreach ($headers as $name => $value) {
            $this->setHeader($name, $value, $overwrite);
        }
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function hasHeaders()
    {
        return count($this->headers) > 0;
    }

    public function setLogFilePath($logFilePath)
    {
        $this->logFilePath = $logFilePath;
    }

    public function getLogFilePath()
    {
        return $this->logFilePath;
    }

    public function setCredentials(ConfigCredentials $configCredentials)
    {
        $this->credentials = $configCredentials;
    }

    /** @return ConfigCredentials */
    public function getCredentials()
    {
        return $this->credentials;
    }
}

<?php

namespace Kutny\HttpClientBundle\Client;

class CurlResourceFactory
{
    public function create(Config $config)
    {
        $curlResource = curl_init($config->getUrl());

        curl_setopt($curlResource, CURLOPT_TIMEOUT, $config->getTimeout());
        curl_setopt($curlResource, CURLOPT_CONNECTTIMEOUT, $config->getConnectionTimeout());
        curl_setopt($curlResource, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlResource, CURLOPT_SSL_VERIFYPEER, $config->getSslCertificateValidation());
        curl_setopt($curlResource, CURLOPT_SSL_VERIFYHOST, $config->getSslCertificateValidation() ? 2 : 0);
        curl_setopt($curlResource, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curlResource, CURLOPT_HEADER, true);
        curl_setopt($curlResource, CURLOPT_CUSTOMREQUEST, $config->getMethod());

        if ($config->getUserAgent()) {
            curl_setopt($curlResource, CURLOPT_USERAGENT, $config->getUserAgent());
        }

        if ($config->getCredentials()) {
            curl_setopt($curlResource, CURLOPT_USERPWD, $config->getCredentials()->getUsername() . ':' . $config->getCredentials()->getPassword());
        }

        if ($config->getCookiesStorageFile()) {
            curl_setopt($curlResource, CURLOPT_COOKIEJAR, $config->getCookiesStorageFile());
            curl_setopt($curlResource, CURLOPT_COOKIEFILE, $config->getCookiesStorageFile());
        }

        if ($config->getMaxRedirects()) {
            curl_setopt($curlResource, CURLOPT_MAXREDIRS, $config->getMaxRedirects());
        }

        if ($config->hasHeaders()) {
            curl_setopt($curlResource, CURLOPT_HTTPHEADER, $this->processHeaders($config->getHeaders()));
        }

        if ($config->getPostData()) {
            curl_setopt($curlResource, CURLOPT_POST, true);
            curl_setopt($curlResource, CURLOPT_POSTFIELDS, $config->getPostData()->getRawPostDataString());
        }

        if ($config->getProxyConfig()) {
            $proxyConfig = $config->getProxyConfig();

            curl_setopt($curlResource, CURLOPT_PROXY, $proxyConfig->getUrl());
            curl_setopt($curlResource, CURLOPT_PROXYPORT, $proxyConfig->getPort());

            if ($proxyConfig->getUsername()) {
                curl_setopt($curlResource, CURLOPT_PROXYUSERPWD, $proxyConfig->getUsername() . ':' . $proxyConfig->getPassword());
            }
        }

        return $curlResource;
    }

    private function processHeaders(array $headers)
    {
        $parsedHeadersArray = array();

        foreach ($headers as $name => $value) {
            $parsedHeadersArray[] = $name . ': ' . $value;
        }

        return $parsedHeadersArray;
    }
}

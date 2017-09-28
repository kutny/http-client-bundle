<?php

namespace Kutny\HttpClientBundle;

use Kutny\HttpClientBundle\Client\Config;
use Kutny\HttpClientBundle\Client\CurlResourceFactory;
use Kutny\HttpClientBundle\Client\RequestFailedException;
use Kutny\HttpClientBundle\Client\RequestInfoFactory;
use Kutny\HttpClientBundle\Client\ResponseParser;
use Kutny\HttpClientBundle\Client\ResponsesContainer;

class Client
{
    private $responseParser;
    private $curlResourceFactory;
    private $requestInfoFactory;

    public function __construct(
        CurlResourceFactory $curlResourceFactory,
        ResponseParser $responseParser,
        RequestInfoFactory $requestInfoFactory
    ) {
        $this->responseParser = $responseParser;
        $this->curlResourceFactory = $curlResourceFactory;
        $this->requestInfoFactory = $requestInfoFactory;
    }

    public function downloadPage(Config $config)
    {
        $curlResource = $this->curlResourceFactory->create($config);

        if ($config->getLogFilePath()) {
            curl_setopt($curlResource, CURLOPT_VERBOSE, true);

            $logHandler = fopen('php://temp', 'rw+');
            curl_setopt($curlResource, CURLOPT_STDERR, $logHandler);
        }

        if ($config->getResponseBodySizeLimit()) {
            $manuallyWrittenData = '';
            $this->handleResponseBodySizeLimit($curlResource, $manuallyWrittenData, $config->getResponseBodySizeLimit());
        }

        if (isset($manuallyWrittenData)) {
            curl_exec($curlResource);
            $data = $manuallyWrittenData;
        }
        else {
            $data = curl_exec($curlResource);
        }

        $curlInfo = curl_getinfo($curlResource);
        $curlErrorMessage = curl_error($curlResource);
        $curlErrorCode = curl_errno($curlResource);

        curl_close($curlResource);

        if (isset($logHandler) && $config->getLogFilePath()) {
            rewind($logHandler);
            $logData = stream_get_contents($logHandler);

            file_put_contents($config->getLogFilePath(), $logData . PHP_EOL, FILE_APPEND);
        }

        if (!$data) {
            throw new RequestFailedException($curlErrorMessage, $curlErrorCode);
        }

        $responses = $this->responseParser->extract($data);
        $requestInfo = $this->requestInfoFactory->create($curlInfo);

        return new ResponsesContainer($responses, $requestInfo);
    }

    private function handleResponseBodySizeLimit(&$curlResource, &$manuallyWrittenData, $limit)
    {
        $allDataLength = 0;

        $writefn = function ($curlResource, $chunk) use ($limit, &$allDataLength, &$manuallyWrittenData) {
            $chunkLength = strlen($chunk);
            $allDataLength += $chunkLength;

            if ($allDataLength >= $limit) {
                return -1;
            }

            $manuallyWrittenData .= $chunk;

            return $chunkLength;
        };

        curl_setopt($curlResource, CURLOPT_RANGE, '0-' . $limit);
        curl_setopt($curlResource, CURLOPT_WRITEFUNCTION, $writefn);
    }
}

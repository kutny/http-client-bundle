<?php

namespace Kutny\HttpClientBundle;

use Kutny\HttpClientBundle\Client\ConfigWithCallback;
use Kutny\HttpClientBundle\Client\CurlResourceFactory;
use Kutny\HttpClientBundle\Client\ErrorCategoryResolver;
use Kutny\HttpClientBundle\Client\Multi\ResultList;
use Kutny\HttpClientBundle\Client\RequestError;
use Kutny\HttpClientBundle\Client\RequestInfoFactory;
use Kutny\HttpClientBundle\Client\ResponseParser;
use Kutny\HttpClientBundle\Client\ResponsesContainer;

class ClientMulti
{
    private $responseParser;
    private $curlResourceFactory;
    private $requestInfoFactory;
    private $errorCategoryResolver;

    public function __construct(
        CurlResourceFactory $curlResourceFactory,
        ResponseParser $responseParser,
        RequestInfoFactory $requestInfoFactory,
        ErrorCategoryResolver $errorCategoryResolver
    ) {
        $this->responseParser = $responseParser;
        $this->curlResourceFactory = $curlResourceFactory;
        $this->requestInfoFactory = $requestInfoFactory;
        $this->errorCategoryResolver = $errorCategoryResolver;
    }

    /**
     * @param ConfigWithCallback[] $configsWithCallback
     */
    public function requests(array $configsWithCallback)
    {
        /* @var ConfigWithCallback[] $configsWithCallback */
        $curlResources = [];
        $logHandlers = [];

        foreach ($configsWithCallback as $index => $configWithCallback) {
            $curlResource = $this->curlResourceFactory->create($configWithCallback->getConfig());

            if ($configWithCallback->getConfig()->getLogFilePath()) {
                curl_setopt($curlResource, CURLOPT_VERBOSE, true);

                $logHandler = fopen('php://temp', 'rw+');
                curl_setopt($curlResource, CURLOPT_STDERR, $logHandler);

                $logHandlers[$index] = $logHandler;
            }

            $curlResources[$index] = $curlResource;
        }

        $mh = curl_multi_init();

        foreach ($curlResources as $curlResource) {
            curl_multi_add_handle($mh, $curlResource);
        }

        $active = null;
        do {
            $mrc = curl_multi_exec($mh, $active);
        }
        while ($mrc == CURLM_CALL_MULTI_PERFORM);

        while ($active && $mrc == CURLM_OK) {
            // Wait for activity on any curl-connection
            if (curl_multi_select($mh) == -1) {
                usleep(1);
            }

            // Continue to exec until curl is ready to
            // give us more data
            do {
                $mrc = curl_multi_exec($mh, $active);
            }
            while ($mrc == CURLM_CALL_MULTI_PERFORM);
        }

        $results = [];
        $errors = [];

        foreach ($curlResources as $index => $curlResource) {
            $configWithCallback = $configsWithCallback[$index];

            $errorMessage = curl_error($curlResource);

            if (!$errorMessage) {
                $successCallback = $configWithCallback->getSuccessCallback();
                $data = curl_multi_getcontent($curlResource);
                $curlInfo = curl_getinfo($curlResource);
                $responseContainer = $this->getResponseContainer($data, $curlInfo);
                $results[] = $successCallback($responseContainer, $configWithCallback->getConfig());
            }
            else {
                $errorCallback = $configWithCallback->getErrorCallback();
                $errorCategory = $this->errorCategoryResolver->resolve($errorMessage);
                $error = new RequestError($errorMessage, $errorCategory);

                $errors[] = $errorCallback($error, $configWithCallback->getConfig());
            }

            curl_multi_remove_handle($mh, $curlResource);
            curl_close($curlResource);

            if (isset($logHandlers[$index]) && $configWithCallback->getConfig()->getLogFilePath()) {
                rewind($logHandlers[$index]);
                $logData = stream_get_contents($logHandlers[$index]);

                file_put_contents($configWithCallback->getConfig()->getLogFilePath(), $logData . PHP_EOL, FILE_APPEND);
            }
        }

        curl_multi_close($mh);

        return new ResultList($results, $errors);
    }

    private function getResponseContainer($data, array $curlInfo)
    {
        $responses = $this->responseParser->extract($data);
        $requestInfo = $this->requestInfoFactory->create($curlInfo);

        return new ResponsesContainer($responses, $requestInfo);
    }
}

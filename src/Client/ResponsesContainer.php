<?php

namespace Kutny\HttpClientBundle\Client;

class ResponsesContainer
{
    private $responses;
    private $requestInfo;

    /**
     * @param Response[] $responses
     */
    public function __construct(array $responses, RequestInfo $requestInfo)
    {
        $this->responses = $responses;
        $this->requestInfo = $requestInfo;
    }

    public function getFirstResponse()
    {
        return $this->responses[0];
    }

    public function getLastResponse()
    {
        $lastIndex = count($this->responses) - 1;

        return $this->responses[$lastIndex];
    }

    public function getResponses()
    {
        return $this->responses;
    }

    public function getResponseCount()
    {
        return count($this->responses);
    }

    public function getResponse($index)
    {
        if (!array_key_exists($index, $this->responses)) {
            throw new \Exception('No response at index: ' . $index);
        }

        return $this->responses[$index];
    }

    public function getRequestInfo()
    {
        return $this->requestInfo;
    }
}

<?php

namespace Kutny\HttpClientBundle\Client;

class RequestInfoFactory
{
    public function create(array $curlInfo)
    {
        return new RequestInfo(
            $curlInfo['url'],
            $curlInfo['total_time']
        );
    }
}

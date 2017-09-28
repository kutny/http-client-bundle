<?php

namespace Kutny\HttpClientBundle\Client;

class ErrorCategoryResolver
{
    public function resolve($errorMessage)
    {
        if (preg_match('~^(Operation timed out after|Connection timed out after|Request timed out|SSL connection timeout|Proxy CONNECT aborted due to timeout|Connection time-out)~', $errorMessage)) {
            return RequestError::CATEGORY_TIMEOUT;
        }

        return null;
    }
}

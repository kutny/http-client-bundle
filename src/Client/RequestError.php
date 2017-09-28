<?php

namespace Kutny\HttpClientBundle\Client;

class RequestError
{
    const CATEGORY_TIMEOUT = 'timeout';

    private $message;
    private $category;

    public function __construct($message, $category)
    {
        $this->message = $message;
        $this->category = $category;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getCategory()
    {
        return $this->category;
    }
}

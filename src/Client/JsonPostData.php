<?php

namespace Kutny\HttpClientBundle\Client;

class JsonPostData implements IPostData
{
    private $postData;

    public function __construct(array $postData)
    {
        $this->postData = $postData;
    }

    public function getRawPostDataString()
    {
        return json_encode($this->postData);
    }
}

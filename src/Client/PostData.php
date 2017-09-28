<?php

namespace Kutny\HttpClientBundle\Client;

class PostData implements IPostData
{
    private $postData;

    public function __construct(array $postData)
    {
        $this->postData = $postData;
    }

    public function getRawPostDataString()
    {
        $keysValues = array();

        foreach ($this->postData as $key => $value) {
            if (is_array($value)) {
                if (count($value) === 0) {
                    $keysValues[] = ($key . '=');
                }
                else {
                    foreach ($value as $index => $arrayValue) {
                        $keysValues[] = ($key . '[' . $index . ']=' . urlencode($arrayValue));
                    }
                }
            }
            else {
                $keysValues[] = ($key . '=' . urlencode($value));
            }
        }

        return implode('&', $keysValues);
    }
}

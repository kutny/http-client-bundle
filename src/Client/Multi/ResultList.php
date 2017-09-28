<?php

namespace Kutny\HttpClientBundle\Client\Multi;

class ResultList
{
    private $successResults;
    private $errors;

    public function __construct(array $successResults = [], array $errors = [])
    {
        $this->successResults = $successResults;
        $this->errors = $errors;
    }

    public function getSuccessResults()
    {
        return $this->successResults;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getAllResults()
    {
        return array_merge($this->successResults, $this->errors);
    }
}

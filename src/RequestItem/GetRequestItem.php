<?php

namespace App\RequestItem;

class GetRequestItem extends RequestItem
{


    const METHOD = 'GET';

    private function __construct($url)
    {
        $this->method = self::METHOD;
        $this->url = $url;
        $this->options = [];
    }

    public static function fromArray(array $requestData): GetRequestItem
    {
        return new self($requestData['url']);
    }
}
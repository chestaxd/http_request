<?php

namespace App\RequestItem;

class PostRequestItem extends RequestItem
{

    const METHOD = 'POST';

    private function __construct($url, $parameters)
    {
        $this->method = self::METHOD;
        $this->url = $url;
        if ($parameters) {
            $this->options['json'] = $parameters;
        }
    }

    public static function fromArray(array $requestData): PostRequestItem
    {
        return new self($requestData['url'], $requestData['parameters'] ?? null);
    }
}
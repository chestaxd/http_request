<?php

namespace App\RequestItem;


class RequestItem
{
    protected string $method;
    protected string $url;
    protected array $options = [];

    public function __construct($method, $url, $options)
    {
        $this->method = strtoupper($method);
        $this->url = $url;
        if ($options) {
            $this->options = $options;
        }
    }

    public function getRequestOptions(): array
    {
        return $this->options;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public static function fromRequestData(array $requestData): self
    {
        return new self($requestData['method'], $requestData['url'], $requestData['options'] ?? false);
    }
}
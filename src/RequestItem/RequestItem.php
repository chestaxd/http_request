<?php

namespace App\RequestItem;

use App\Entity\Proxy;

abstract class RequestItem
{
    protected string $method;
    protected string $url;
    protected array $options = [];


    public abstract static function fromArray(array $requestData);

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
}
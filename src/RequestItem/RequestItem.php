<?php

namespace App\RequestItem;

use App\Entity\Proxy;

abstract class RequestItem
{
    protected string $method;
    protected string $url;
    protected array $options = [];


    public abstract static function fromArray(array $requestData);

    public function getOptions(): array
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

    public function setProxy(Proxy $proxy): static
    {
        $this->options['proxy'] = $proxy->getProxy();
        return $this;
    }
}
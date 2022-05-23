<?php

namespace App\RequestItem;

class RequestItem
{
    protected string $method;
    protected string $url;
    protected array $options = [];

    public function __construct(private readonly InitStrategy $strategy, array $data)
    {
        $this->init($data);
    }

    private function init(array $requestData)
    {
        $this->strategy->init($requestData, $this);
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

    /**
     * @param string $method
     */
    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

}
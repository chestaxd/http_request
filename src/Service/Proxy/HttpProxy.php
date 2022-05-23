<?php

namespace App\Service\Proxy;

use App\Entity\Proxy;
use App\Repository\ProxyRepository;
use App\Service\ProxyList\ProxyList;

class HttpProxy implements ProxyItem
{

    public function __construct(private readonly ProxyList $proxyList) { }

    public function get(): string
    {
        return $this->initProxy($this->proxyList->getRandomProxy());
    }

    private function initProxy(Proxy $proxy): string
    {
        return 'http://' . $proxy->getLogin() . ':' . $proxy->getPassword() . '@' . $proxy->getAddress() . ':' . $proxy->getPort();
    }
}
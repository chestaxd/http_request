<?php

namespace App\Service\Proxy;

use App\Entity\Proxy;
use App\Repository\ProxyRepository;

class DBProxyList implements ProxyList
{
    public function __construct(private ProxyRepository $proxyRepository) { }

    public function getRandomProxy(): Proxy
    {
        return $this->proxyRepository->getRandomProxy();
    }

    public function getProxyList(): array
    {
        $proxyEntities = $this->proxyRepository->findAll();

        return array_map(function ($proxy) {
            return $proxy->getProxy();
        }, $proxyEntities);
    }

    public function addProxy(Proxy $proxy): static
    {
        // TODO: Implement addProxy() method.
    }

    public function deleteProxy(Proxy $proxy): static
    {
        // TODO: Implement deleteProxy() method.
    }
}
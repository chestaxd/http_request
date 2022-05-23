<?php

namespace App\Service\ProxyList;

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
        return $this->proxyRepository->findAll();
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
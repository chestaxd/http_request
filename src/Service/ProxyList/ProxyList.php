<?php

namespace App\Service\ProxyList;

use App\Entity\Proxy;

interface ProxyList
{
    public function getRandomProxy(): Proxy;

    public function getProxyList(): array;

    public function addProxy(Proxy $proxy): static;

    public function deleteProxy(Proxy $proxy): static;

}
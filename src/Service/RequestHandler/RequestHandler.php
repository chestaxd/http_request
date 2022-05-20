<?php

namespace App\Service\RequestHandler;

use App\RequestItem\RequestItem;
use App\Entity\Proxy;
use App\Entity\Response;
use App\Service\Proxy\ProxyList;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RequestHandler implements RequestHandlerInterface
{


    public function __construct(
        private ProxyList           $proxyList,
        private HttpClientInterface $client
    )
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function handle(RequestItem $requestItem, $useProxy = false): Response
    {
        $requestOptions = $requestItem->getRequestOptions();
        if ($useProxy) {
            $requestOptions['proxy'] = $this->getProxyObject()->getProxy();
        }
        $responseData = $this->client->request(
            $requestItem->getMethod(),
            $requestItem->getUrl(),
            $requestOptions
        );
        return new Response($responseData->getStatusCode(), $responseData->toArray());
    }

    private function getProxyObject(): Proxy
    {
        return $this->proxyList->getRandomProxy();
    }
}
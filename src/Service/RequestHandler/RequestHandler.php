<?php

namespace App\Service\RequestHandler;

use App\Entity\Response;
use App\RequestItem\RequestItem;
use App\Service\Proxy\HttpProxy;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RequestHandler implements RequestHandlerInterface
{


    public function __construct(
        private HttpProxy           $httpProxy,
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
            $requestOptions['proxy'] = $this->httpProxy->get();
        }
        $responseData = $this->client->request(
            $requestItem->getMethod(),
            $requestItem->getUrl(),
            $requestOptions
        );
        return new Response($responseData->getStatusCode(), json_decode($responseData->getContent(false), true));
    }

//    private function getProxyObject(): Proxy
//    {
//        return $this->proxyList->getRandomProxy();
//    }
}
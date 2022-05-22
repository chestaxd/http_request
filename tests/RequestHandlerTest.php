<?php

namespace App\Tests;

use App\Entity\Response;
use App\RequestItem\RequestItem;
use App\Service\Proxy\ProxyList;
use App\Service\RequestHandler\RequestHandler;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class RequestHandlerTest extends KernelTestCase
{
    public function testSuccessRequestHandler(): void
    {
        $expectedResponseData = ['test' => 'success'];
        $requestData = ['method' => 'GET', 'url' => 'https://example.com/success'];
        $proxyList = $this->createMock(ProxyList::class);
        $mockResponse = new MockResponse(json_encode($expectedResponseData), ['http_code' => 200]);
        $mockHttpClient = new MockHttpClient($mockResponse, $requestData['url']);
        $requestItem = RequestItem::fromRequestData($requestData);
        $requestHandler = new RequestHandler($proxyList, $mockHttpClient);
        $response = $requestHandler->handle($requestItem);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame($expectedResponseData, $response->getData());
    }

    public function testErrorRequestHandler(): void
    {
        $proxyList = $this->createMock(ProxyList::class);


        $responses = [
            new MockResponse('', ['http_code' => 500]),
            new MockResponse('', ['http_code' => 404])
        ];

        $mockHttpClient = new MockHttpClient();
        $mockHttpClient->setResponseFactory($responses);
        $requestData = ['method' => 'GET', 'url' => 'https://example.com/error'];
        $requestItem = RequestItem::fromRequestData($requestData);
        $requestHandler = new RequestHandler($proxyList, $mockHttpClient);
        $response500 = $requestHandler->handle($requestItem);
        $response404 = $requestHandler->handle($requestItem);
        $this->assertSame(500, $response500->getCode());
        $this->assertInstanceOf(Response::class, $response500);
        $this->assertSame(404, $response404->getCode());
        $this->assertInstanceOf(Response::class, $response404);
    }
}

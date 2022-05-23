<?php

namespace App\Tests;


use App\Command\ExecuteRequestFromQueueCommand;
use App\Entity\Proxy;
use App\Entity\Request;
use App\Enum\Status;
use App\Service\ProxyList\ProxyList;
use App\Service\RequestHandler\RequestHandler;
use App\Service\RequestHandler\RequestHandlerInterface;
use App\Service\RequestJobList;
use App\Service\ResponseWriter\ResponseWriterInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class RequestFromQueueCommandTest extends KernelTestCase
{
    private ParameterBagInterface $parameterBag;
    private ResponseWriterInterface $responseWriter;
    private RequestJobList $requestJonListMock;
    private ProxyList $proxyList;


    protected function setUp(): void
    {
        $container = self::getContainer();
        $this->parameterBag = $container->get(ParameterBagInterface::class);
        $this->responseWriter = $this->createStub(ResponseWriterInterface::class);
        $this->requestJonListMock = $this->getMockBuilder(RequestJobList::class)->disableOriginalConstructor()->getMock();
        $this->proxyList = $this->getMockBuilder(ProxyList::class)->disableOriginalConstructor()->getMock();

    }


    public function testSuccessfulExecution(): void
    {
        $requestData = ['method' => 'GET', 'url' => 'https://example.com/success'];

        $job = new Request();
        $job->setUseProxy(false);
        $job->setSaveResponse(false);
        $job->setStatus(Status::PENDING);
        $job->setRequestData($requestData);

        $this->requestJonListMock->expects($this->once())->method('getAvailableJob')->willReturn($job);
        $mockHttpClient = new MockHttpClient(new MockResponse('', ['http_code' => 200]), $requestData['url']);
        $requestHandler = new RequestHandler($this->proxyList, $mockHttpClient);
        $command = new ExecuteRequestFromQueueCommand(
            $requestHandler,
            $this->responseWriter,
            $this->requestJonListMock,
            $this->parameterBag);
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
        $commandTester->assertCommandIsSuccessful();
    }

    public function testThrowException(): void
    {
        $requestData = ['method' => 'GET', 'url' => 'https://example.com/error'];

        $job = new Request();
        $job->setUseProxy(false);
        $job->setSaveResponse(false);
        $job->setStatus(Status::PENDING);
        $job->setRequestData($requestData);
        $this->requestJonListMock->expects($this->once())->method('getAvailableJob')->willReturn($job);

        $requestHandler = $this->getMockBuilder(RequestHandlerInterface::class)
            ->disableOriginalConstructor()->getMock();
        $requestHandler->expects($this->once())->method('handle')->will($this->throwException(new TransportException()));

        $command = new ExecuteRequestFromQueueCommand(
            $requestHandler,
            $this->responseWriter,
            $this->requestJonListMock,
            $this->parameterBag);
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());
    }

}

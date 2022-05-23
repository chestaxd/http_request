<?php

namespace App\Command;

use App\RequestItem\GetRequest;
use App\RequestItem\InitStrategy;
use App\RequestItem\RequestItem;
use App\Service\RequestHandler\RequestHandlerInterface;
use App\Service\RequestJobList;
use App\Service\ResponseWriter\ResponseWriterInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsCommand(
    name: 'app:execute-request-from-queue',
    description: 'Get a request and execute it',
)]
class ExecuteRequestFromQueueCommand extends Command
{
    public function __construct(
        private RequestHandlerInterface $requestHandler,
        private ResponseWriterInterface $responseWriter,
        private RequestJobList          $requestJobList,
        private ParameterBagInterface   $parameterBag,
        string                          $name = null
    )
    {
        parent::__construct($name);
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $job = $this->requestJobList->getAvailableJob();
        if (!$job) {
            $io->warning('Job not found');
            return Command::SUCCESS;
        }
        $io->info('Start job:' . $job->getId());
        try {

            $requestData = $job->getRequestData();
            $request = $this->getStrategy($requestData['method']);
            $requestItem = new RequestItem($request, $requestData);
            $response = $this->requestHandler->handle($requestItem, $job->isUseProxy());
            if ($job->isSaveResponse()) {
                $io->info('Write Response:' . $job->getId());
                $response->setRequest($job);
                $this->responseWriter->write($response);
            }
            $this->requestJobList->done($job);
            $io->info('Job done:' . $job->getId());
            //catch Http exceptions
        } catch (\Exception $exception) {
            $io->error('Job Error:' . $job->getId());
            $attempts = $this->parameterBag->get('app.attempt_count');
            $job->setError($exception->getMessage());
            $job->getExecutionsWithError() < $attempts ?
                $this->requestJobList->retry($job) :
                $this->requestJobList->fail($job);
            return Command::FAILURE;
        }
        return Command::SUCCESS;
    }

    private function getStrategy($method): InitStrategy
    {
        return match (strtolower($method)) {
            'get' => new GetRequest(),
            default => throw new NotFoundHttpException('Strategy not found')
        };
    }
}

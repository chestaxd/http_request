<?php

namespace App\Command;

use App\Entity\Request;
use App\RequestItem\RequestItem;
use App\Service\RequestHandler\RequestHandlerInterface;
use App\Service\RequestJobList;
use App\Service\ResponseWriter\ResponseWriterInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:execute-request',
    description: 'Add a short description for your command',
)]
class ExecuteRequestCommand extends Command
{
    const REQUEST_INTERVAL = 1; //in seconds
    const MAX_EXECUTE_ERRORS = 3;

    public function __construct(
        private RequestHandlerInterface $requestHandler,
        private ResponseWriterInterface $responseWriter,
        private RequestJobList          $requestJobList,
        string                          $name = null
    )
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->addArgument('requestId', InputArgument::REQUIRED, 'Request Id');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $job = $this->requestJobList->getJobById($input->getArgument('requestId'));
        if (!$job) {
            $io->error('Request not found');
            return Command::INVALID;
        }
        return $this->doJob($job, $io);
    }

    private function doJob(Request $job, SymfonyStyle $io): bool
    {
        try {
            if ($job->getExecutionsWithError() >= self::MAX_EXECUTE_ERRORS) {
                $io->error('Job Error:' . $job->getId());
                $this->requestJobList->fail($job);
                return Command::FAILURE;
            }
            $io->info('Start job:' . $job->getId());
            $requestItem = RequestItem::fromRequestData($job->getRequestData());
            $response = $this->requestHandler->handle($requestItem, $job->isUseProxy());

            if ($job->isSaveResponse()) {
                $io->info('Write Response:' . $job->getId());
                $response->setRequest($job);
                $this->responseWriter->write($response);
            }
            $this->requestJobList->done($job);
            $io->info('Job done:' . $job->getId());
            return Command::SUCCESS;
        } catch
        (\Exception $exception) {
            sleep(self::REQUEST_INTERVAL);
            $job->setError($exception->getMessage());
            $job->incrementsError();
            return $this->doJob($job, $io);
        }
    }
}
<?php

namespace App\Service;

use App\Entity\Request;
use App\Enum\Status;
use App\Repository\RequestRepository;

class RequestJobList
{

    public function __construct(
        private $attemptInterval,
        private readonly RequestRepository $requestRepository
    )
    {
    }

    public function getJobById(int $id): ?Request
    {
        return $this->requestRepository->find($id);
    }

    public function getAvailableJob(): ?Request
    {
        return $this->requestRepository->getRequestJob();
    }

    public function done(Request $request): Request
    {
        $request->setStatus(Status::DONE);
        $request->setFinishedAt(new \DateTime());
        $this->requestRepository->add($request, true);
        return $request;
    }

    /**
     * @throws \Exception
     */
    public function retry(Request $request): Request
    {
        $request->incrementsError();
        $request->setNextAttemptAt(
            (new \DateTime())->add(new \DateInterval('PT' . $this->attemptInterval . 'M'))
        );
        $this->requestRepository->add($request, true);
        return $request;
    }

    public function fail(Request $request): Request
    {
        $request->setStatus(Status::ERROR);
        $this->requestRepository->add($request, true);
        return $request;
    }
}
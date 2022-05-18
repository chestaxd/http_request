<?php

namespace App\Service\ResponseWriter;

use App\Entity\Response;
use App\Repository\ResponseRepository;

class ResponseWriter implements ResponseWriterInterface
{

    public function __construct(private readonly ResponseRepository $responseRepository) { }

    public function write(Response $response)
    {
        $this->responseRepository->add($response, true);
    }
}
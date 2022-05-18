<?php

namespace App\Service\ResponseWriter;

use App\Entity\Response;

interface ResponseWriterInterface
{
    public function write(Response $response);

}
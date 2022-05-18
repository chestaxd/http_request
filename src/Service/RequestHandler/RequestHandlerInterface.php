<?php

namespace App\Service\RequestHandler;

use App\Entity\Response;
use App\RequestItem\RequestItem;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

interface RequestHandlerInterface
{

    /**
     * @throws TransportExceptionInterface
     */
    public function handle(RequestItem $requestItem): Response;

}